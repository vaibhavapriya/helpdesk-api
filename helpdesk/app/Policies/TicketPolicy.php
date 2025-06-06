<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ticket;
use Illuminate\Auth\Access\Response;
class TicketPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
        /**
     * Determine whether the user can view the model.
     */
    //In controller $this->authorize('update', $ticket);
    public function view(User $user, ticket $ticket): bool
    {
        // return false;
        return $user->id === $ticket->requester_id || $user->isAdmin();

    }
}
