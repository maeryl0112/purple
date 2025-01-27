<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlotAvailable
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;
    /**
     * Create a new event instance.
     */
    public function __construct($appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return new Channel('slot-available');
    }

    public function broadcastWith()
    {
        return [
            'service_name' => $this->appointment->service->name,
            'date' => $this->appointment->date,
            'time' => $this->appointment->time,
        ];
    }
}
