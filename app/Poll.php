<?php

namespace App;

use App\Events\PollDeleting;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\ConcurrentEditionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Poll extends Model
{
    use Notifiable;

    /**
     * Fire a PollDeleting event when deleting a poll
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'deleting' => PollDeleting::class,
    ];

    public $timestamps = false;

    // Prevents ID incrementing and allows accessing poll->id
    public $incrementing = false;

    /**
     * Get the Comments for the Poll.
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    /**
     * Get the Slots for the Poll.
     */
    public function slots()
    {
        return $this->hasMany('App\Slot');
    }

    /**
     * Get the Votes for the Poll.
     */
    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    /**
     * @param Form $form
     * @return array
     */
    public static function createPoll(Form $form) {
        // Generate poll IDs, loop while poll ID already exists
        if (empty($form->id)) { // User wants us to generate an id for him
            do {
                $poll_id = Token::getToken(16);
                $exiting_poll_id = Poll::where('id', $poll_id)->first();
            } while(!empty($exiting_poll_id));
            $admin_poll_id = $poll_id . Token::getToken(8);

        } else { // User has chosen the poll id
            $poll_id = $form->id;
            do {
                $admin_poll_id = Token::getToken(24);
                $existing_admin_poll_id = Poll::where('admin_id', $admin_poll_id)->first();
            } while(!empty($existing_admin_poll_id));

        }

        // Insert poll + slots
        self::insertPoll($poll_id, $admin_poll_id, $form);
        Slot::insertSlots($poll_id, $form->getChoices());

        return array($poll_id, $admin_poll_id);
    }

    private static function insertPoll($poll_id, $admin_poll_id, $form) {
        $poll = new Poll();

        $poll->id = $poll_id;
        $poll->admin_id = $admin_poll_id;
        $poll->title = $form->title;
        $poll->description = $form->description;
        $poll->admin_name = $form->admin_name;
        $poll->admin_mail = $form->admin_mail;
        $poll->end_date = date('Y-m-d H:i:s', $form->end_date);
        $poll->format = $form->format;
        $poll->editable = ($form->editable >= config('laradate.NOT_EDITABLE')
            && $form->editable <= config('laradate.EDITABLE_BY_OWN')) ? $form->editable : config('laradate.EDITABLE_BY_OWN');
        $poll->receiveNewVotes = $form->receiveNewVotes ? 1 : 0;
        $poll->receiveNewComments = $form->receiveNewComments ? 1 : 0;
        $poll->hidden = $form->hidden ? 1 : 0;
        $poll->password_hash = $form->password_hash;
        $poll->results_publicly_visible = $form->results_publicly_visible ? 1 : 0;
        $poll->value_max = $form->useValueMax ? $form->valueMax : 0;

        $poll->save();
    }

    public static function computeBestChoices($votes) {
        $result = ['y' => [0], 'inb' => [0]];
        foreach ($votes as $vote) {
            $choices = str_split($vote->choices);
            foreach ($choices as $i => $choice) {
                if (!isset($result['y'][$i])) {
                    $result['inb'][$i] = 0;
                    $result['y'][$i] = 0;
                }
                if ($choice == 1) {
                    $result['inb'][$i]++;
                }
                if ($choice == 2) {
                    $result['y'][$i]++;
                }
            }
        }

        return $result;
    }

    /**
     * @return int The max timestamp allowed for expiry date
     */
    public static function maxExpiryDate() {
        return time() + (86400 * config('laradate.default_poll_duration'));
    }

    /**
     * @return int The min timestamp allowed for expiry date
     */
    public static function minExpiryDate() {
        return time() + 86400;
    }

    /**
     * Verify if the current session allows to access given poll.
     *
     * @param $poll \stdClass The poll which we seek access
     * @return bool true if the current session can access this poll
     */
    public static function canAccess($poll) {
        if (is_null($poll->password_hash)) {
            return true;
        }

        $currentPassword = session()->get('poll_security.'.$poll->id);
        if (!empty($currentPassword) && password_verify($currentPassword, $poll->password_hash)) {
            return true;
        }

        session()->forget('poll_security.'.$poll->id);
        session()->save();
        return false;
    }

    /**
     * Search polls in database.
     *
     * @return array The found polls
     */
    public static function findAdminPolls() {
        // Polls
        $polls = Poll::with('votes')->orderBy('title', 'asc');

        if (request()->input('poll')) {
            $polls = $polls->orWhere('id', 'LIKE', '%'.request()->input('poll').'%');
        }

        if (request()->input('title')) {
            $polls = $polls->orWhere('title', 'LIKE', '%'.request()->input('title').'%');
        }

        if (request()->input('name')) {
            $polls = $polls->orWhere('admin_name', 'LIKE', '%'.request()->input('name').'%');
        }

        if (request()->input('mail')) {
            $polls = $polls->orWhere('admin_mail', 'LIKE', '%'.request()->input('mail').'%');
        }

        return $polls->paginate(15);
    }

    /**
     * This method purges all old polls (the ones with end_date in past).
     *
     * @return bool true is action succeeded
     */
    public static function purgeOldPolls() {
        $pdoDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
        switch ($pdoDriver) {
            case 'pgsql':
                $oldPolls = Poll::whereRaw("end_date +  INTERVAL '". config('laradate.PURGE_DELAY') . " DAY' < NOW() AND end_date IS NOT NULL")->limit(20)->get();
                break;
            case 'mysql':
            default:
                $oldPolls = Poll::whereRaw("DATE_ADD(end_date, INTERVAL " . config('laradate.PURGE_DELAY') . " DAY) < NOW() AND end_date != 0")->limit(20)->get();
                break;
        }
        $count = count($oldPolls);

        if ($count > 0) {
            Log::info('EXPIRATION: Going to purge ' . $count . ' poll(s)...');

            foreach ($oldPolls as $poll) {
                if ($poll->delete()) {
                    Log::info('EXPIRATION_SUCCESS: id: ' . $poll->id . ', title:' . $poll->title . ', format: '.$poll->format . ', admin: ' . $poll->admin_name);
                } else {
                    Log::info('EXPIRATION_FAILED: id: ' . $poll->id . ', title:' . $poll->title . ', format: '.$poll->format . ', admin: ' . $poll->admin_name);
                }
            }
        }

        return $count;
    }
}
