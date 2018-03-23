<?php

namespace App;

use App\Exceptions\AlreadyExistsException;
use App\Exceptions\ConcurrentEditionException;
use App\Exceptions\ConcurrentVoteException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Vote extends Model
{
    public $timestamps = false;

    /**
     * @param $poll_id
     * @param $vote_id
     * @param $name
     * @param $choices
     * @param $slots_hash
     * @return mixed
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     */
    public static function updateVote($poll_id, $vote_id, $name, $choices, $slots_hash) {
        $poll = Poll::find($poll_id);

        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        self::checkMaxVotes($choices, $poll, $poll_id);

        // Check if slots are still the same
        self::checkThatSlotsDidntChanged($poll, $slots_hash);

        // Update vote
        $choices = implode($choices);

        $vote = Vote::where('poll_id', $poll_id)->where('id', $vote_id)->first();
        $vote->name = $name;
        $vote->choices = $choices;
        return $vote->save();
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $choices
     * @param $slots_hash
     * @return Vote
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     */
    public static function addVote($poll_id, $name, $choices, $slots_hash) {
        $poll = Poll::find($poll_id);

        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        self::checkMaxVotes($choices, $poll, $poll_id);

        // Check if slots are still the same
        self::checkThatSlotsDidntChanged($poll, $slots_hash);

        // Check if vote already exists
        $existsByPollIdAndName = Vote::where('poll_id', $poll_id)->where('name', $name)->first();
        if (!empty($existsByPollIdAndName)) {
            throw new AlreadyExistsException();
        }

        // Insert new vote
        $choices = implode($choices);
        $token = Token::getToken(16);

        $vote = new Vote();
        $vote->poll_id = $poll_id;
        $vote->name = $name;
        $vote->choices = $choices;
        $vote->uniqId = $token;
        $vote->save();
        return $vote;
    }

    /**
     * @param $poll_id
     * @param $insert_position
     * @return int
     */
    public static function insertDefault($poll_id, $insert_position) {
        return DB::update('UPDATE `' . env('DB_TABLE_PREFIX', '') . 'votes` SET choices = CONCAT(SUBSTRING(choices, 1, ?), " ", SUBSTRING(choices, ?)) WHERE poll_id = ?', [
            $insert_position,
            $insert_position + 1,
            $poll_id
        ]);
    }

    /**
     * Delete all votes made on given moment index.
     *
     * @param $poll_id int The ID of the poll
     * @param $index int The index of the vote into the poll
     * @return bool|null true if action succeeded.
     */
    public static function deleteByIndex($poll_id, $index) {
        return DB::update('UPDATE `' . env('DB_TABLE_PREFIX', '') . 'votes` SET choices = CONCAT(SUBSTR(choices, 1, ?), SUBSTR(choices, ?)) WHERE poll_id = ?', [
            $index, $index + 2, $poll_id]
        );
    }

    /**
     * This method checks if the hash send by the user is the same as the computed hash.
     *
     * @param $poll /stdClass The poll
     * @param $slots_hash string The hash sent by the user
     * @throws ConcurrentEditionException Thrown when hashes are different
     */
    private static function checkThatSlotsDidntChanged($poll, $slots_hash) {
        $slots = self::allSlotsByPoll($poll);
        if ($slots_hash !== Slot::hash($slots)) {
            throw new ConcurrentEditionException();
        }
    }

    /**
     * This method checks if the votes don't conflict the maximum votes constraint
     *
     * @param $user_choice
     * @param \stdClass $poll
     * @param string $poll_id
     * @throws ConcurrentVoteException
     */
    private static function checkMaxVotes($user_choice, $poll, $poll_id) {
        $votes = Vote::where('poll_id', $poll_id)->orderBy('id')->get();
        if (count($votes) <= 0) {
            return;
        }
        $best_choices = Poll::computeBestChoices($votes);
        foreach ($best_choices['y'] as $i => $nb_choice) {
            // if for this option we have reached maximum value and user wants to add itself too
            if ($poll->valueMax != null && $nb_choice >= $poll->valueMax && $user_choice[$i] === "2") {
                throw new ConcurrentVoteException();
            }
        }
    }


    public static function allSlotsByPoll($poll) {
        $slots = Slot::where('poll_id', $poll->id);
        if ($poll->format == 'D') {
            $slots = $slots->orderBy('title');
        } else {
            $slots = $slots->orderBy('id');
        }

        return $slots->get();
    }

    /**
     * @param $votes
     * @return array
     */
    public static function split($votes) {
        $splitted = array();
        foreach ($votes as $vote) {
            $obj = new \stdClass();
            $obj->id = $vote->id;
            $obj->name = $vote->name;
            $obj->uniqId = $vote->uniqId;
            $obj->choices = str_split($vote->choices);

            $splitted[] = $obj;
        }

        return $splitted;
    }
}
