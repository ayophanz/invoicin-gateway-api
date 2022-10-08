<?php

namespace App\Listeners;

use App\Events\RegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Jobs\OrgConfirmationJob;

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
        OrgConfirmationJob::dispatch($event->organization);
    }
}
