<?php

namespace App\Events;

use App\Poll;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when deleting a poll
 * Class PollDeleting
 * @package App\Events
 */
class PollDeleting
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $poll;

    /**
     * Create a new event instance.
     *
     * @param Poll $poll
     */
    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('poll-deleting');
    }
}
