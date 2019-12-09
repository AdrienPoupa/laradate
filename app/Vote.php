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
     * The vote's Poll
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poll()
    {
        return $this->belongsTo('App\Poll');
    }

    /**
     * @param $poll
     * @param $voteId
     * @param $name
     * @param $choices
     * @param $slots_hash
     * @return mixed
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     */
    public static function updateVote($poll, $voteId, $name, $choices, $slots_hash) {
        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        self::checkMaxVotes($choices, $poll);

        // Check if slots are still the same
        self::checkThatSlotsDidntChanged($poll, $slots_hash);

        // Update vote
        $choices = implode($choices);

        $vote = Vote::where('poll_id', $poll->id)->where('id', $voteId)->first();
        $vote->name = $name;
        $vote->choices = $choices;
        return $vote->save();
    }

    /**
     * @param $pollId
     * @param $name
     * @param $choices
     * @param $slots_hash
     * @return Vote
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     */
    public static function addVote($poll, $name, $choices, $slots_hash) {

        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        self::checkMaxVotes($choices, $poll);

        // Check if slots are still the same
        self::checkThatSlotsDidntChanged($poll, $slots_hash);

        // Check if vote already exists
        $existsByPollIdAndName = Vote::where('poll_id', $poll->id)->where('name', $name)->first();
        if (!empty($existsByPollIdAndName)) {
            throw new AlreadyExistsException();
        }

        // Insert new vote
        $choices = implode($choices);
        $token = Token::getToken(16);

        $vote = new Vote();
        $vote->poll_id = $poll->id;
        $vote->name = $name;
        $vote->choices = $choices;
        $vote->uniqId = $token;
        $vote->save();

        return $vote;
    }

    /**
     * @param $pollId
     * @param $insertPosition
     * @return int
     */
    public static function insertDefault($pollId, $insertPosition) {
        return DB::update("UPDATE " . env('DB_TABLE_PREFIX', '') . "votes SET choices = CONCAT(SUBSTRING(choices, 1, ?), ' ', SUBSTRING(choices, ?)) WHERE poll_id = ?", [
            $insertPosition,
            $insertPosition + 1,
            $pollId
        ]);
    }

    /**
     * Delete all votes made on given moment index.
     *
     * @param $pollId int The ID of the poll
     * @param $index int The index of the vote into the poll
     * @return bool|null true if action succeeded.
     */
    public static function deleteByIndex($pollId, $index) {
        return DB::update("UPDATE " . env('DB_TABLE_PREFIX', '') . "votes SET choices = CONCAT(SUBSTR(choices, 1, ?), SUBSTR(choices, ?)) WHERE poll_id = ?",
            [$index, $index + 2, $pollId]
        );
    }

    /**
     * This method checks if the hash send by the user is the same as the computed hash.
     *
     * @param $poll /stdClass The poll
     * @param $slotsHash string The hash sent by the user
     * @throws ConcurrentEditionException Thrown when hashes are different
     */
    private static function checkThatSlotsDidntChanged($poll, $slotsHash) {
        $slots = self::allSlotsByPoll($poll);
        if ($slotsHash !== Slot::hash($slots)) {
            throw new ConcurrentEditionException();
        }
    }

    /**
     * This method checks if the votes don't conflict the maximum votes constraint
     *
     * @param $userChoice
     * @param \stdClass $poll
     * @throws ConcurrentVoteException
     */
    private static function checkMaxVotes($userChoice, $poll) {
        $votes = Vote::where('poll_id', $poll->id)->orderBy('id')->get();
        if (count($votes) <= 0) {
            return;
        }
        $best_choices = Poll::computeBestChoices($votes);
        foreach ($best_choices['y'] as $i => $nb_choice) {
            // if for this option we have reached maximum value and user wants to add itself too
            if ($poll->value_max != null && $nb_choice >= $poll->value_max && $userChoice[$i] === 2) {
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
        $split = array();
        foreach ($votes as $vote) {
            $obj = new \stdClass();
            $obj->id = $vote->id;
            $obj->name = $vote->name;
            $obj->uniqId = $vote->uniqId;
            $obj->choices = str_split($vote->choices);

            $split[] = $obj;
        }

        return $split;
    }
}
