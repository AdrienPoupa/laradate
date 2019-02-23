<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PollAdminCreated extends Mailable
{
    use Queueable, SerializesModels;

    private $poll_id;
    private $form;

    /**
     * Create a new message instance.
     *
     * @param $admin_poll_id
     */
    public function __construct($admin_poll_id)
    {
        $this->poll_id = $admin_poll_id;
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
                    ->subject('[' . config('app.name') . '][' . __('mail.Author\'s message') . '] ' . __('generic.Poll') . ': ' . $this->form->title)
                    ->view('mail.classic_poll_admin', ['url' => \App\Utils::getPollUrl($this->poll_id, true)]);
    }
}
