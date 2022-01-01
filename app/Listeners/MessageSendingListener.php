<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MessageSendingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (!env("DEBUG_EMAILS")) { return; }
 
        $event->message->addBcc(env('VEHICLE_IMPORTED_NOTIFICATION_EMAIL_BCC', 'jydp1313@gmail.com'));
    }
}
