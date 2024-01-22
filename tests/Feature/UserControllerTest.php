<?php

namespace Feature;

use Tests\TestCase;
use App\Models\User;
use Tests\Concerns\WithUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithUsers;

    /** @test */
    public function it_can_list_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->moderatorUser)
            ->getJson(route('users.index'))
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $user->name]);
    }
}
