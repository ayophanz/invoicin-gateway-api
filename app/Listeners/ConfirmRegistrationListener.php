<?php

namespace App\Listeners;

use App\Events\RegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\ConfirmRegistrationJob;

class ConfirmRegistrationListener
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
        ConfirmRegistrationJob::dispatch($event->user);
    }
}
