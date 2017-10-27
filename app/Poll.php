<?php

namespace App;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\ConcurrentEditionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Poll extends Model
{
    public $timestamps = false;

    // Prevents ID incrementing and allows accessing poll->id
    public $incrementing = false;

    /**
     * @param Form $form
     * @return array
     */
    public static function createPoll(Form $form) {
        // Generate poll IDs, loop while poll ID already exists
        if (empty($form->id)) { // User wants us to generate an id for him
            do {
                $poll_id = self::random(16);
                $exiting_poll_id = Poll::where('id', $poll_id)->first();
            } while(!empty($exiting_poll_id));
            $admin_poll_id = $poll_id . self::random(8);

        } else { // User has chosen the poll id
            $poll_id = $form->id;
            do {
                $admin_poll_id = self::random(24);
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

        $poll->save();
    }

    /**
     * Delete the entire given poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    public static function deleteEntirePoll($poll_id) {
        $poll = Poll::find($poll_id);
        Log::info('DELETE_POLL: id:'.$poll->id.', format:'.$poll->format.', admin:'.$poll->admin_name.', mail:'.$poll->admin_mail);

        // Delete the entire poll
        Vote::where('poll_id', $poll_id)->delete();
        Comment::where('poll_id', $poll_id)->delete();
        Slot::where('poll_id', $poll_id)->delete();

        return $poll->delete();
    }

    public function findAllByAdminMail($mail) {
        return $this->pollRepository->findAllByAdminMail($mail);
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

    private static function random($length) {
        return Token::getToken($length);
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
     * Return the list of all polls.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>..., 'mail'=>...]
     * @param int $page The page index (O = first page)
     * @param int $limit The limit size
     * @return array ['polls' => The {$limit} polls, 'count' => Entries found by the query, 'total' => Total count]
     */
    public static function findAllPolls($search, $page, $limit) {
        $start = $page * $limit;
        $polls = Poll::findAll($search, $start, $limit);
        $count = Poll::countPolls($search);
        $total = Poll::countPolls();

        return ['polls' => $polls, 'count' => $count, 'total' => $total];
    }
    /**
    * Search polls in database.
    *
    * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>..., 'mail'=>...]
    * @param int $start The number of first entry to select
    * @param int $limit The number of entries to find
    * @return array The found polls
    */
    public static function findAll($search, $start, $limit) {
        // Polls
        $query = DB::select('
        SELECT p.*,
               (SELECT count(1) FROM `' . env('DB_TABLE_PREFIX', '') . 'votes` v WHERE p.id=v.poll_id) votes
          FROM `' . env('DB_TABLE_PREFIX', '') . 'polls` p
         WHERE (:id = "" OR p.id LIKE :id2)
           AND (:title = "" OR p.title LIKE :title2)
           AND (:name = "" OR p.admin_name LIKE :name2)
           AND (:mail = "" OR p.admin_mail LIKE :mail2)
         ORDER BY p.title ASC
         LIMIT :start, :limit
         ', [
             'id' => $search['poll'] . '%',
             'id2' => $search['poll'] . '%',
             'title' => '%' . $search['title'] . '%',
             'title2' => '%' . $search['title'] . '%',
             'name' => '%' . $search['name'] . '%',
             'name2' => '%' . $search['name'] . '%',
             'mail' => '%' . $search['mail'] . '%',
             'mail2' => '%' . $search['mail'] . '%',
             'start' => $start,
             'limit' => $limit,
        ]);

        return $query;
    }

    /**
     * Get the total number of polls in database.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>...]
     * @return int The number of polls
     */
    public static function countPolls($search = null) {
        // Total count
        $query = DB::select('
        SELECT count(1) nb
          FROM `' . env('DB_TABLE_PREFIX', '') . 'polls` p
         WHERE (:id = "" OR p.id LIKE :id2)
           AND (:title = "" OR p.title LIKE :title2)
           AND (:name = "" OR p.admin_name LIKE :name2)
         ORDER BY p.title ASC', [
            'id' => $search == null ? '' : $search['poll'] . '%',
            'id2' => $search == null ? '' : $search['poll'] . '%',
            'title' => $search == null ? '' : '%' . $search['title'] . '%',
            'title2' => $search == null ? '' : '%' . $search['title'] . '%',
            'name' => $search == null ? '' : '%' . $search['name'] . '%',
            'name2' => $search == null ? '' : '%' . $search['name'] . '%',
        ]);

        return $query[0]->nb;
    }

    /**
     * This method purges all old polls (the ones with end_date in past).
     *
     * @return bool true is action succeeded
     */
    public static function purgeOldPolls() {
        $oldPolls = Poll::findOldPolls();
        $count = count($oldPolls);

        if ($count > 0) {
            Log::info('EXPIRATION: Going to purge ' . $count . ' poll(s)...');

            foreach ($oldPolls as $poll) {
                if (Poll::purgePollById($poll->id)) {
                    Log::info('EXPIRATION_SUCCESS: id: ' . $poll->id . ', title:' . $poll->title . ', format: '.$poll->format . ', admin: ' . $poll->admin_name);
                } else {
                    Log::info('EXPIRATION_FAILED: id: ' . $poll->id . ', title:' . $poll->title . ', format: '.$poll->format . ', admin: ' . $poll->admin_name);
                }
            }
        }

        return $count;
    }

    /**
     * Find old polls. Limit: 20.
     *
     * @return array Array of old polls
     */
    public static function findOldPolls() {
        $query = DB::select('SELECT * FROM `' . env('DB_TABLE_PREFIX', '') . 'polls` WHERE DATE_ADD(`end_date`, INTERVAL ' . config('laradate.PURGE_DELAY') . ' DAY) < NOW() AND `end_date` != 0 LIMIT 20');

        return $query;
    }

    /**
     * This method deletes all data about a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    public static function purgePollById($poll_id) {
        $done = true;

        $done &= Comment::where('poll_id', $poll_id)->delete();
        $done &= Vote::where('poll_id', $poll_id)->delete();
        $done &= Slot::where('poll_id', $poll_id)->delete();
        $done &= Poll::where('poll', $poll_id)->delete();

        return $done;
    }
}
