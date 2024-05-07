<?php

namespace App\Events;

use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CountdownStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $roomId;
    public int $questionId;

    /**
     * Create a new event instance.
     *
     * @param int $roomId
     * @param int $questionId
     */
    public function __construct($roomId, $questionId)
    {
        try {
            if (!isset($roomId) || !isset($questionId)) {
                throw new \Exception("Room ID or Question ID is not set.");
            }

            $this->roomId = $roomId;
            $this->questionId = $questionId;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating SetQuestion event: ' . $e->getMessage());

            // You can choose to throw the exception again if you want to handle it elsewhere
            // throw $e;
        }
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'roomId' => $this->roomId,
            'questionId' => $this->questionId,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('count-start');
    }
}
