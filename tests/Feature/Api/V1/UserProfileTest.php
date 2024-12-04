<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;
    use BootstrapsApiV1;

    public function testItValidatesWhenTooShort(): void
    {
        $this->get($this->apiUrl('GetUserProfile', ['u' => 'i']))
            ->assertJsonValidationErrors([
                'u',
            ]);
    }

    public function testItValidatesWhenTooLong(): void
    {
        $this->get($this->apiUrl('GetUserProfile', ['u' => str_repeat('x', 25)]))
            ->assertJsonValidationErrors([
                'u',
            ]);
    }

    public function testGetUserProfileUnknownUser(): void
    {
        $this->get($this->apiUrl('GetUserProfile', ['u' => 'nonExistant']))
            ->assertNotFound()
            ->assertJson([]);
    }

    public function testGetUserProfileAuthUser(): void
    {
        $this->get($this->apiUrl('GetUserProfile'))
            ->assertSuccessful()
            ->assertJson([
                'User' => $this->user->User,
                'UserPic' => sprintf("/UserPic/%s.png", $this->user->User),
                'MemberSince' => $this->user->created_at->toDateTimeString(),
                'RichPresenceMsg' => ($this->user->RichPresenceMsg) ? $this->user->RichPresenceMsg : null,
                'LastGameID' => $this->user->LastGameID,
                'ContribCount' => $this->user->ContribCount,
                'ContribYield' => $this->user->ContribYield,
                'TotalPoints' => $this->user->RAPoints,
                'TotalSoftcorePoints' => $this->user->RASoftcorePoints,
                'TotalTruePoints' => $this->user->TrueRAPoints,
                'Permissions' => $this->user->getAttribute('Permissions'),
                'Untracked' => $this->user->Untracked,
                'ID' => $this->user->ID,
                'UserWallActive' => $this->user->UserWallActive,
                'Motto' => $this->user->Motto,
            ]);
    }

    public function testGetUserProfile(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->get($this->apiUrl('GetUserProfile', ['u' => $user->User]))
            ->assertSuccessful()
            ->assertJson([
                'User' => $user->User,
                'UserPic' => sprintf("/UserPic/%s.png", $user->User),
                'MemberSince' => $user->created_at->toDateTimeString(),
                'RichPresenceMsg' => ($user->RichPresenceMsg) ? $user->RichPresenceMsg : null,
                'LastGameID' => $user->LastGameID,
                'ContribCount' => $user->ContribCount,
                'ContribYield' => $user->ContribYield,
                'TotalPoints' => $user->RAPoints,
                'TotalSoftcorePoints' => $user->RASoftcorePoints,
                'TotalTruePoints' => $user->TrueRAPoints,
                'Permissions' => $user->getAttribute('Permissions'),
                'Untracked' => $user->Untracked,
                'ID' => $user->ID,
                'UserWallActive' => $user->UserWallActive,
                'Motto' => $user->Motto,
            ]);
    }
}
