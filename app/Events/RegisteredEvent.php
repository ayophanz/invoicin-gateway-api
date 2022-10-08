<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Organization;

class RegisteredEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $organization;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Organization $organization)
    {
        $this->user         = $user;
        $this->organization = $organization;
    }
}
