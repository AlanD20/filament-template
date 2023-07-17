<?php

namespace App\Services\Logger;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;

class NotificationLogger
{
    /**
     * Log the notification
     */
    public function handle(NotificationSent|NotificationFailed $event)
    {
        //
    }
}
