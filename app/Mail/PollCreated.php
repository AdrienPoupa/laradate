<?php

namespace App\Mail;

use App\Utils;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PollCreated extends Mailable
{
    use Queueable, SerializesModels;

    private $poll_id;
    private $form;

    /**
     * Create a new message instance.
     *
     * @param $poll_id
     */
    public function __construct($poll_id)
    {
        $this->poll_id = $poll_id;
        $this->form = session()->get('form');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('laradate.NO_REPLY_MAIL'), config('app.name'))
                    ->to($this->form->admin_mail)
                    ->subject('[' . config('app.name') . '][' . __('mail.For sending to the polled users') . '] ' . __('generic.Poll') . ': ' . $this->form->title)
                    ->view('mail.classic_poll', [
                        'admin_name' => $this->form->admin_name,
                        'title' => $this->form->title,
                        'url' => Utils::getPollUrl($this->poll_id),
                    ]);
    }
}
