<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RandomQuestionWriterSelected implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $selectedParticipantId;

    /**
     * Create a new event instance.
     */
    public function __construct($roomId, $selectedParticipantId)
    {
        $this->roomId = $roomId;
        $this->selectedParticipantId = $selectedParticipantId;
    }

    public function broadcastWith(): array
    {
        return [
            'roomId' => $this->roomId,
            'selectedParticipantId' => $this->selectedParticipantId,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('random-participant');
    }
}
