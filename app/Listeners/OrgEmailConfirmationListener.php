<?php

namespace App\Listeners;

use App\Events\RegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class OrgEmailConfirmationListener
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
     * @param  \App\Events\RegisteredEvent  $event
     * @return void
     */
    public function handle(RegisteredEvent $event)
    {
        \Log::debug($event->organization);
    }
}
