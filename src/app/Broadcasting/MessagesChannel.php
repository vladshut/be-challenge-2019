<?php

namespace App\Broadcasting;

use App\Room;
use App\User;

class MessagesChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param User $user
     * @param Room $room
     * @return array|bool
     */
    public function join(User $user, Room $room)
    {
        return true;
        return $room->users()->has($user);
    }
}
