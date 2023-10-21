<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Answer;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnswerPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Answer $answer)
    {
        return true;
    }

    public function delete(User $user, Answer $answer)
    {
        return true;
    }

    public function publish(User $user, Answer $answer)
    {
        return $user->role === 'admin';
    }
}
