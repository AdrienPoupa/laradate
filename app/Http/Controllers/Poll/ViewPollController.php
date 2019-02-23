<?php

namespace App\Http\Controllers\Poll;

use App\Comment;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\ConcurrentEditionException;
use App\Exceptions\ConcurrentVoteException;
use App\Http\Controllers\Controller;
use App\Mail\SendPollNotification;
use App\Poll;
use App\Slot;
use App\Utils;
use App\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ViewPollController extends Controller
{
    public function index(Request $request, $poll_id, $editingVoteId = 0)
    {
        $accessGranted = true;
        $resultPubliclyVisible = true;
        $slots = array();
        $votes = array();
        $comments = array();
        $editableVoteHtml = null;

        $poll = Poll::find($poll_id);

        if (!$poll) {
            return response()->view('errors.error', [
                'title' => __('error.This poll doesn\'t exist !')
            ], 404);
        }

        $editedVoteUniqueId = session()->get('UserVotes')[$poll_id];

        if (!is_null($poll->password_hash)) {

            // If we came from password submission
            if ($request->has('password')) {
                session()->put('poll_security.'.$poll->id, $request->input('password'));
                session()->save();
            }

            if (!Poll::canAccess($poll)) {
                $accessGranted = false;
            }
            $resultPubliclyVisible = $poll->results_publicly_visible;

            if (!$accessGranted && !empty($password)) {
                session()->flash('danger', __('password.Wrong password'));
            } else if (!$accessGranted && !$resultPubliclyVisible) {
                session()->flash('danger', __('password.You have to provide a password to access the poll.'));
            } else if (!$accessGranted && $resultPubliclyVisible) {
                session()->flash('danger', __('password.You have to provide a password so you can participate to the poll.'));
            }
        }

        // We allow actions only if access is granted
        if ($accessGranted) {

            // -------------------------------
            // Something to save (edit or add)
            // -------------------------------

            if ($request->has('save') && $request->input('save') != '') { // Save edition of an old vote
                $name = $request->input('name');
                $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
                $choices = Utils::filterArray($request->input('choices'), FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => config('laradate.CHOICE_REGEX')]]);
                $slotsHash = Utils::isValidMd5($request->input('control'));

                if (empty($editedVote)) {
                    session()->flash('danger', __('error.Something is going wrong...'));
                } else if (count($choices) != count($request->input('choices'))) {
                    session()->flash('danger', __('error.There is a problem with your choices'));
                } else {
                    // Update vote
                    try {
                        $result = Vote::updateVote($poll_id, $editedVote, $name, $choices, $slotsHash);
                        if ($result) {
                            if ($poll->editable == config('laradate.EDITABLE_BY_OWN')) {
                                $editedVoteUniqueId = filter_input(INPUT_POST, 'edited_vote', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => config('laradate.POLL_REGEX')]]);
                                $editableVoteHtml = $this->getMessageForOwnVoteEditableVote($editedVoteUniqueId, $poll_id, $name);
                            } else {
                                session()->flash('success', __('poll.Update vote succeeded'));
                            }
                            Mail::send(new SendPollNotification($poll, SendPollNotification::UPDATE_VOTE, $name));
                        } else {
                            session()->flash('danger', __('error.Update vote failed'));
                        }
                    } catch (ConcurrentEditionException $cee) {
                        session()->flash('danger', __('error.Poll has been updated before you vote'));
                    }
                }
            } elseif ($request->has('save')) { // Add a new vote
                $name = $request->input('name');
                $choices = Utils::filterArray($request->input('choices'), FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => config('laradate.CHOICE_REGEX')]]);
                $slotsHash = Utils::isValidMd5($request->input('control'));

                if ($name == null) {
                    session()->flash('danger', __('error.The name is invalid.'));
                }
                else if (count($choices) != count($request->input('choices'))) {
                    session()->flash('danger', __('error.There is a problem with your choices'));
                } else {
                    // Add vote
                    try {
                        $result = Vote::addVote($poll_id, $name, $choices, $slotsHash);
                        if ($result) {
                            if ($poll->editable == config('laradate.EDITABLE_BY_OWN')) {
                                $editedVoteUniqueId = $result->uniqId;
                                $editableVoteHtml = $this->getMessageForOwnVoteEditableVote($editedVoteUniqueId, $poll_id, $name);
                            } else {
                                session()->flash('success', __('poll.Adding the vote succeeded'));
                            }
                            Mail::send(new SendPollNotification($poll, SendPollNotification::ADD_VOTE, $name));
                        } else {
                            session()->flash('danger', __('error.Adding vote failed'));
                        }
                    } catch (AlreadyExistsException $aee) {
                        session()->flash('danger', __('error.You already voted'));
                    } catch (ConcurrentEditionException $cee) {
                        session()->flash('danger', __('error.Poll has been updated before you vote'));
                    } catch (ConcurrentVoteException $cve) {
                        session()->flash('danger', __('error.Your vote wasn\'t counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry.'));
                    }
                }
            }
        }

        // Retrieve data
        if ($resultPubliclyVisible || $accessGranted) {
            $slots = Vote::allSlotsByPoll($poll);
            $votes = Vote::where('poll_id', $poll_id)->orderBy('id')->get();
            $comments = Comment::where('poll_id', $poll_id)->orderBy('id')->get();
        }

        // Assign data to template
        return view('poll', [
            'poll_id' => $poll_id,
            'poll' => $poll,
            'title' => __('generic.Poll') . ' - ' . $poll->title,
            'expired' => strtotime($poll->end_date) < time(),
            'deletion_date' => strtotime($poll->end_date) + 60 * 86400,
            'slots' => $poll->format === 'D' ? Slot::split($slots) : $slots,
            'slots_hash' => Slot::hash($slots),
            'votes' => Vote::split($votes),
            'best_choices' => Poll::computeBestChoices($votes),
            'comments' => $comments,
            'editingVoteId' => $editingVoteId,
            'admin' => false,
            'hidden' => $poll->hidden,
            'accessGranted' => $accessGranted,
            'resultPubliclyVisible' => $resultPubliclyVisible,
            'editedVoteUniqueId' => $editedVoteUniqueId,
            'editableVoteHtml' => $editableVoteHtml,
        ]);
    }

    private function getMessageForOwnVoteEditableVote($editedVoteUniqueId, $poll_id, $name) {
        session()->put('UserVotes.'.$poll_id, $editedVoteUniqueId);
        session()->save();
        $urlEditVote = Utils::getPollUrl($poll_id, false, $editedVoteUniqueId);

        $html = view('part.editable_link', [
            'message' => __('poll.Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'),
            'link' => $urlEditVote,
            'linkTitle' => __('poll_results.Edit the line: :s', ['s' => $name]),
            'includeTemplate' => config('laradate.use_smtp'),
            'poll_id' => $poll_id,
            'editedVoteUniqueId' => $editedVoteUniqueId
        ])->render();

        return $html;
    }
}
