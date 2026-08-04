<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('conversation.{a}.{b}', function ($user, $a, $b) {
    // Only allow the two participants to listen
    $a = (int) $a;
    $b = (int) $b;
    return $user->id === $a || $user->id === $b;
});
