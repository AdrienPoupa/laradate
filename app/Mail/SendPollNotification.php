<?php

namespace App\Mail;

use App\Utils;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPollNotification extends Mailable
{
    use Queueable, SerializesModels;

    const UPDATE_VOTE = 1;
    const ADD_VOTE = 2;
    const ADD_COMMENT = 3;
    const UPDATE_POLL = 10;
    const DELETED_POLL = 11;

    private $poll;
    private $type;
    private $name;

    /**
     * Create a new message instance.
     * @param $poll
     * @param $type
     * @param $name string The name user who triggered the notification
     * @internal param $this ->poll Poll The poll
     * @internal param $this ->type int cf: Constants on the top of this page
     *
     */
    public function __construct($poll, $type, $name='')
    {
        $this->poll = $poll;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * Send a notification to the poll admin to notify him about an update.
     */
    public function build() {
        if (!session()->has('mail_sent')) {
            session()->put('mail_sent', []);
            session()->save();
        }

        if (self::isParticipation()) {
            $translationString = 'Poll\'s participation: :s';
        } else {
            $translationString = 'Notification of poll: :s';
        }

        $subject = '[' . config('app.name') . '] ' . __('mail.'.$translationString, ['s' => $this->poll->title]);

        $message = '';

        $urlSondage = Utils::getPollUrl($this->poll->admin_id, true);
        $link = '<a href="' . $urlSondage . '">' . $urlSondage . '</a>' . "\n\n";

        switch ($this->type) {
            case self::UPDATE_VOTE:
                $message .= $this->name . ' ';
                $message .= __('mail.updated a vote.\nYou can find your poll at the link') . " :\n\n";
                $message .= $link;
                break;
            case self::ADD_VOTE:
                $message .= $this->name . ' ';
                $message .= __('mail.filled a vote.\nYou can find your poll at the link') . " :\n\n";
                $message .= $link;
                break;
            case self::ADD_COMMENT:
                $message .= $this->name . ' ';
                $message .= __('mail.wrote a comment.\nYou can find your poll at the link') . " :\n\n";
                $message .= $link;
                break;
            case self::UPDATE_POLL:
                $message = __('mail.Someone just change your poll available at the following link :s.', ['s' => Utils::getPollUrl($this->poll->admin_id, true)]) . "\n\n";
                break;
            case self::DELETED_POLL:
                $message = __('mail.Someone just delete your poll :s.', ['s' => $this->poll->title]) . "\n\n";
                break;

        }

        return $this->from(config('laradate.NO_REPLY_MAIL'), config('app.name'))
            ->to($this->poll->admin_mail)
            ->subject($subject)
            ->view('mail.send_notification', ['messageMail' => $message]);
    }

    function isParticipation()
    {
        return $this->type >= self::UPDATE_POLL;
    }
}
