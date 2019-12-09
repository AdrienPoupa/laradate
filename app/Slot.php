<?php

namespace App;

use App\Exceptions\MomentAlreadyExistsException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Slot extends Model
{
    public $timestamps = false;

    /**
     * The slot's Poll
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poll()
    {
        return $this->belongsTo('App\Poll');
    }

    /**
     * Insert a bulk of slots.
     *
     * @param int $pollId
     * @param array $choices
     */
    public static function insertSlots($pollId, $choices) {
        foreach ($choices as $choice) {

            // We prepared the slots (joined by comas)
            $joinedSlots = '';
            $first = true;
            foreach ($choice->getSlots() as $slot) {
                if ($first) {
                    $joinedSlots = $slot;
                    $first = false;
                } else {
                    $joinedSlots .= ',' . $slot;
                }
            }

            $slot = new Slot();

            $slot->poll_id = $pollId;
            $slot->title = $choice->getName();

            // We execute the insertion
            if (empty($joinedSlots)) {
                $slot->moments = null;
            } else {
                $slot->moments = $joinedSlots;
            }

            $slot->save();
        }
    }

    public static function split($slots)
    {
        $splitted = array();
        foreach ($slots as $slot) {
            $obj = new \stdClass();
            $obj->day = $slot->title;
            $obj->moments = explode(',', $slot->moments);

            $splitted[] = $obj;
        }

        return $splitted;
    }

    /**
     * @param $slots array The slots to hash
     * @return string The hash
     */
    public static function hash($slots)
    {
        // If password is required and access is not granted yet, $slots will be empty
        if (!$slots) {
            return null;
        }
        return md5(array_reduce($slots->toArray(), function ($carry, $item) {
            return $carry . $item['id'] . '@' . $item['moments'] . ';';
        }));
    }

    /**
     * Delete a slot from a poll.
     *
     * @param object $poll The ID of the poll
     * @param object $slot The slot information (datetime + moment)
     * @return bool true if action succeeded
     */
    public static function deleteDateSlot($poll, $slot) {
        Log::info('DELETE_SLOT: id:' . $poll->id . ', slot:' . json_encode($slot));

        $datetime = $slot->title;
        $moment = $slot->moment;

        $slots = Vote::allSlotsByPoll($poll);

        // We can't delete the last slot
        if ($poll->format == 'D' && count($slots) === 1 && strpos($slots[0]->moments, ',') === false) {
            return false;
        } elseif ($poll->format == 'A' && count($slots) === 1) {
            return false;
        }

        $index = 0;
        $indexToDelete = -1;
        $newMoments = [];

        // Search the index of the slot to delete
        foreach ($slots as $aSlot) {
            $moments = explode(',', $aSlot->moments);

            foreach ($moments as $rowMoment) {
                if ($datetime == $aSlot->title) {
                    if ($moment == $rowMoment) {
                        $indexToDelete = $index;
                    } else {
                        $newMoments[] = $rowMoment;
                    }
                }
                $index++;
            }
        }

        // Remove votes
        Vote::deleteByIndex($poll->id, $indexToDelete);
        if (count($newMoments) > 0) {
            $updatedSlot = Slot::where('poll_id', $poll->id)->where('title', $datetime)->first();
            $updatedSlot->moments = implode(',', $newMoments);
            $updatedSlot->save();
        } else {
            Slot::where('poll_id', $poll->id)->where('title', $datetime)->delete();
        }

        return true;
    }

    public static function deleteClassicSlot($poll, $slotTitle) {
        Log::info('DELETE_SLOT: id:' . $poll->id . ', slot:' . $slotTitle);

        $slots = Vote::allSlotsByPoll($poll);

        if (count($slots) === 1) {
            return false;
        }

        $index = 0;
        $indexToDelete = -1;

        // Search the index of the slot to delete
        foreach ($slots as $aSlot) {
            if ($slotTitle == $aSlot->title) {
                $indexToDelete = $index;
            }
            $index++;
        }

        // Remove votes
        Vote::deleteByIndex($poll->id, $indexToDelete);
        Slot::where('poll_id', $poll->id)->where('title', $slotTitle)->delete();

        return true;
    }

    /**
     * Add a new slot to a date poll. And insert default values for user's votes.
     * <ul>
     *  <li>Create a new slot if no one exists for the given date</li>
     *  <li>Create a new moment if a slot already exists for the given date</li>
     * </ul>
     *
     * @param $pollId int The ID of the poll
     * @param $datetime int The datetime
     * @param $newMoment string The moment's name
     * @throws MomentAlreadyExistsException When the moment to add already exists in database
     */
    public static function addDateSlot($pollId, $datetime, $newMoment) {
        Log::info('ADD_COLUMN: id:' . $pollId . ', datetime:' . $datetime . ', moment:' . $newMoment);

        $slots = Slot::where('poll_id', $pollId)->orderBy('id')->get();
        $result = Slot::findInsertPosition($slots, $datetime);

        if ($result->slot != null) {
            $slot = $result->slot;
            $moments = explode(',', $slot->moments);

            // Check if moment already exists (maybe not necessary)
            if (in_array($newMoment, $moments)) {
                throw new MomentAlreadyExistsException();
            }

            // Update found slot
            $moments[] = $newMoment;
            $updatedSlot = Slot::where('poll_id', $pollId)->where('title', $datetime)->first();
            $updatedSlot->moments = implode(',', $moments);
            $updatedSlot->save();

        } else {
            $newSlot = new Slot();
            $newSlot->poll_id = $pollId;
            $newSlot->title = $datetime;
            $newSlot->moments = $newMoment;
            $newSlot->save();
        }

        Vote::insertDefault($pollId, $result->insert);
    }

    /**
     * Add a new slot to a classic poll. And insert default values for user's votes.
     * <ul>
     *  <li>Create a new slot if no one exists for the given title</li>
     * </ul>
     *
     * @param $pollId int The ID of the poll
     * @param $title int The title
     * @throws MomentAlreadyExistsException When the moment to add already exists in database
     */
    public static function addClassicSlot($pollId, $title) {
        Log::info('ADD_COLUMN: id:' . $pollId . ', title:' . $title);

        $slots = Slot::where('poll_id', $pollId)->orderBy('id')->get();

        // Check if slot already exists
        $titles = array_map(function ($slot) {
            return $slot['title'];
        }, $slots->toArray());
        if (in_array($title, $titles)) {
            // The moment already exists
            throw new MomentAlreadyExistsException();
        }

        // New slot
        $newSlot = new Slot();
        $newSlot->poll_id = $pollId;
        $newSlot->title = $title;
        $newSlot->save();
        // Set default votes
        Vote::insertDefault($pollId, count($slots));
    }

    /**
     * This method finds where to insert a datatime+moment into a list of slots.<br/>
     * Return the {insert:X}, where X is the index of the moment into the whole poll (ex: X=0 => Insert to the first column).
     * Return {slot:Y}, where Y is not null if there is a slot existing for the given datetime.
     *
     * @param $slots array All the slots of the poll
     * @param $datetime int The datetime of the new slot
     * @return \stdClass An object like this one: {insert:X, slot:Y} where Y can be null.
     */
    private static function findInsertPosition($slots, $datetime) {
        $result = new \stdClass();
        $result->slot = null;
        $result->insert = 0;

        // Sort slots before searching where to insert
        Slot::sort($slots);

        // Search where to insert new column
        foreach ($slots as $k=>$slot) {
            $rowDatetime = $slot->title;
            $moments = explode(',', $slot->moments);

            if ($datetime == $rowDatetime) {
                // Here we have to insert at the end of a slot
                $result->insert += count($moments);
                $result->slot = $slot;
                break;
            } elseif ($datetime < $rowDatetime) {
                // We have to insert before this slot
                break;
            } else {
                $result->insert += count($moments);
            }
        }

        return $result;
    }

    /**
     * @param $slots
     * @return mixed
     */
    public static function sort($slots) {
        $array = $slots->toArray();
        uasort($array, function ($a, $b) {
            return $a['title'] > $b['title'];
        });
        return $array;
    }

}
