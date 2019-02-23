<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPollList extends Mailable
{
    use Queueable, SerializesModels;

    private $recipient;
    private $polls;

    /**
     * Create a new message instance.
     *
     * @param $recipient
     * @param $polls
     */
    public function __construct($recipient, $polls)
    {
        $this->recipient = $recipient;
        $this->polls = $polls;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = __('findPolls.List of your polls').' - '.config('app.name');

        return $this->from(config('laradate.NO_REPLY_MAIL'), config('app.name'))
                    ->to($this->recipient)
                    ->subject($subject)
                    ->view('mail.find_polls', ['polls' => $this->polls]);
    }
}
