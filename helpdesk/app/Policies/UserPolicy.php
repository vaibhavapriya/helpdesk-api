<?php

namespace App\Policies;

use App\Models\User;

class userPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function view(User $user, User $profile): bool
    {
        // return false;
        return $user->id === $profile->id || $user->isAdmin();

    }
}
