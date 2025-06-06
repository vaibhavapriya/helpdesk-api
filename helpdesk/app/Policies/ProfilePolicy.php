<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class ProfilePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function view(User $user, profile $profile): bool
    {
        // return false;
        return $user->id === $profile->user_id || $user->isAdmin();

    }
}
