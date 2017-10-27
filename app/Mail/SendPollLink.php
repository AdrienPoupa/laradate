<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPollLink extends Mailable
{
    use Queueable, SerializesModels;

    private $recipient;
    private $poll;
    private $editedVoteUniqueId;

    /**
     * Create a new message instance.
     *
     * @param $recipient
     * @param $poll
     * @param $editedVoteUniqueId
     */
    public function __construct($recipient, $poll, $editedVoteUniqueId)
    {
        $this->recipient = $recipient;
        $this->poll = $poll;
        $this->editedVoteUniqueId = $editedVoteUniqueId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '[' . config('app.name') . ']['.__('editLink.REMINDER').'] '.__('editLink.Edit link for poll ":s"', ['s' => $this->poll->title]);

        session()->put('Common.'.config('laradate.SESSION_EDIT_LINK_TIME'), time());
        session()->save();

        return $this->from(config('laradate.NO_REPLY_MAIL'), config('app.name'))
                    ->to($this->recipient)
                    ->subject($subject)
                    ->view('mail.remember_edit_link', [
                        'poll' => $this->poll,
                        'poll_id' => $this->poll->id,
                        'editedVoteUniqueId' => $this->editedVoteUniqueId,
                    ]);
    }
}
