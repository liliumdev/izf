<?php

namespace Tests\Concerns;

use App\Models\User;

trait WithUsers
{
    protected User $adminUser;

    protected User $moderatorUser;

    public function setUpWithUsers(): void
    {
        $this->adminUser = User::factory()->admin()->create();
        $this->moderatorUser = User::factory()->moderator()->create();
    }
}
