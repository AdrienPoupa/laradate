<?php

namespace App\Listeners;

use App\Comment;
use App\Events\PollDeleting;
use App\Slot;
use App\Vote;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Delete all the related models of the poll
 * Class PollDeletion
 * @package App\Listeners
 */
class PollDeletion
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
     * @param PollDeleting $event
     * @return void
     */
    public function handle(PollDeleting $event)
    {
        $poll = $event->poll;
        Log::info('DELETE_POLL: id:'.$poll->id.', format:'.$poll->format.', admin:'.$poll->admin_name.', mail:'.$poll->admin_mail);

        // Delete the related models of the poll
        $poll->votes()->delete();
        $poll->slots()->delete();
        $poll->comments()->delete();
    }
}
