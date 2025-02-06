<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshData implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $users;

    /**
     * Create a new event instance.
     */
    public function __construct(array $users)
    {
        $this->users = $users;
        Log::info('RefreshUser event created', ['user_ids' => array_map(fn($user) => $user->id, $this->users)]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];
        foreach ($this->users as $user) {
            $channels[] = new PrivateChannel("App.Models.User.{$user->id}");
            Log::info('Broadcasting on channel', ['channel' => "App.Models.User.{$user->id}"]);
        }
        return $channels;
    }
}
