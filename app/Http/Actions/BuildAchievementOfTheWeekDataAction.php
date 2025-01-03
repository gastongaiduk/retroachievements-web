<?php

declare(strict_types=1);

namespace App\Http\Actions;

use App\Models\Achievement;
use App\Models\EventAchievement;
use App\Models\StaticData;
use App\Platform\Data\EventAchievementData;

class BuildAchievementOfTheWeekDataAction
{
    // TODO remove $staticData arg once event is actually run using EventAchievements
    public function execute(?StaticData $staticData): ?EventAchievementData
    {
        $achievementOfTheWeek = EventAchievement::active()
            ->whereNotNull('active_from')
            ->whereNotNull('active_until')
            ->whereHas('achievement.game', function ($query) { // only from the current AotW event
                $query->where('Title', 'like', '%of the week%');
            })
            ->whereRaw(dateCompareStatement('active_until', 'active_from', '< 20')) // ignore AotM achievements - don't specifically look for 7 days because of the extended duration of the week 52 event
            ->with(['achievement.game', 'sourceAchievement.game'])
            ->first();

        if (!$achievementOfTheWeek || !$achievementOfTheWeek->source_achievement_id) {
            if (!$staticData?->Event_AOTW_AchievementID) {
                return null;
            }

            $targetAchievementId = $staticData->Event_AOTW_AchievementID;

            $achievement = Achievement::find($targetAchievementId);
            if (!$achievement) {
                return null;
            }

            // make a new EventAchievment object (and modify the related records) to
            // mimic the behavior of a valid EventAchievement. DO NOT SAVE THESE!
            $achievement->game->ForumTopicID = $staticData->Event_AOTW_ForumID;

            $achievementOfTheWeek = new EventAchievement();
            $achievementOfTheWeek->setRelation('achievement', $achievement);
            $achievementOfTheWeek->setRelation('sourceAchievement', $achievement);
        }

        $data = EventAchievementData::from($achievementOfTheWeek)->include(
            'achievement.id',
            'achievement.title',
            'achievement.description',
            'achievement.badgeUnlockedUrl',
            'sourceAchievement.game.badgeUrl',
            'sourceAchievement.game.system.iconUrl',
            'sourceAchievement.game.system.nameShort',
            'forumTopicId',
            'activeUntil',
        );

        return $data;
    }
}
