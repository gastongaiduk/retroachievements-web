<?php

 /*
 *  API_GetUserGameLeaderboards - returns a list of Leaderboards for the given User and GameID
 *    i : gameID
 *    u : username
 *    o : offset - number of entries to skip (default: 0)
 *    c : count - number of entries to return (default: 200, max: 500)
 *
 *  int         Count                       number of leaderboard records returned in the response
 *  int         Total                       number of leaderboard records the user has actually on the game
 *  array       Results
 *   object      [value]
 *    int        ID                         unique identifier of the leaderboard
 *    string     RankAsc                    string value of true or false for if the leaderboard views a lower score as better
 *    string     Title                      the title of the leaderboard
 *    string     Description                the description of the leaderboard
 *    string     Format                     the format of the leaderboard (see: ValueFormat enum)
 *    object     UserEntry                  details of the requested user's leaderboard entry
 *     object      [value]
 *      string     User                     username
 *      int        Score                    raw value score
 *      string     FormattedScore           formatted string value of score
 *      int        Rank                     user's leaderboard rank
 *      string     DateUpdated              an ISO8601 timestamp string for when the entry was updated
 */

use App\Models\Game;
use App\Models\LeaderboardEntry;
use App\Models\User;
use App\Platform\Enums\ValueFormat;
use App\Support\Rules\CtypeAlnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

$input = Validator::validate(Arr::wrap(request()->query()), [
    'i' => ['required', 'min:1'],
    'u' => ['required', 'min:2', 'max:20', new CtypeAlnum()],
    'o' => ['sometimes', 'integer', 'min:0', 'nullable'],
    'c' => ['sometimes', 'integer', 'min:1', 'max:500', 'nullable'],
]);

$offset = $input['o'] ?? 0;
$count = $input['c'] ?? 200;

$user = User::firstWhere('User', request()->query('u'));
if (!$user) {
    return response()->json(['User not found'], 404);
}

$game = Game::firstWhere("ID", request()->query('i'));
if (!$game) {
    return response()->json(['Game not found'], 404);
}

if ($game->leaderboards()->count() === 0) {
    return response()->json(['Game has no leaderboards'], 422);
}

$userLeaderboardEntriesCount = LeaderboardEntry::where('user_id', $user->id)
    ->whereIn('leaderboard_id', function ($query) use ($game) {
        $query->select('ID')
              ->from('LeaderboardDef')
              ->where('GameID', $game->ID);
    })
    ->whereNull('deleted_at')
    ->count();

if ($userLeaderboardEntriesCount === 0) {
    return response()->json(['User has no leaderboards on this game'], 422);
}

$leaderboardEntries = LeaderboardEntry::select('leaderboard_entries.*')
    ->addSelect([
        'calculated_rank' => LeaderboardEntry::from('leaderboard_entries as entries_bis')
            ->join('LeaderboardDef as leaderboardDefBis', 'entries_bis.leaderboard_id', '=', 'leaderboardDefBis.ID')
            ->whereColumn('entries_bis.leaderboard_id', 'leaderboard_entries.leaderboard_id')
            ->whereNull('entries_bis.deleted_at')
            ->whereNull('leaderboardDefBis.deleted_at')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('leaderboardDefBis.LowerIsBetter', 1)
                      ->whereColumn('entries_bis.score', '<', 'leaderboard_entries.score');
                })->orWhere(function ($q) {
                    $q->where('leaderboardDefBis.LowerIsBetter', 0)
                      ->whereColumn('entries_bis.score', '>', 'leaderboard_entries.score');
                });
            })
            ->selectRaw('COUNT(*) + 1'),
    ])
    ->join('LeaderboardDef', 'leaderboard_entries.leaderboard_id', '=', 'LeaderboardDef.ID')
    ->where('LeaderboardDef.GameID', $game->ID)
    ->where('leaderboard_entries.user_id', $user->id)
    ->whereNull('leaderboard_entries.deleted_at')
    ->whereNull('LeaderboardDef.deleted_at')
    ->with('leaderboard')
    ->orderBy('LeaderboardDef.ID', 'asc')
    ->skip($offset)
    ->take($count)
    ->get();

$results = [];
foreach ($leaderboardEntries as $leaderboardEntry) {
    $results[] = [
        'ID' => $leaderboardEntry->leaderboard->ID,
        'RankAsc' => $leaderboardEntry->leaderboard->LowerIsBetter ? 'false' : 'true',
        'Title' => $leaderboardEntry->leaderboard->Title,
        'Description' => $leaderboardEntry->leaderboard->Description,
        'Format' => $leaderboardEntry->leaderboard->Format,
        'UserEntry' => [
            'User' => $user->display_name,
            'Score' => $leaderboardEntry->score,
            'FormattedScore' => ValueFormat::format($leaderboardEntry->score, $leaderboardEntry->leaderboard->Format),
            'Rank' => $leaderboardEntry->calculated_rank,
            'DateUpdated' => $leaderboardEntry->updated_at->toIso8601String(),
        ],
    ];
}

return response()->json([
    'Count' => count($results),
    'Total' => $userLeaderboardEntriesCount,
    'Results' => $results,
]);
