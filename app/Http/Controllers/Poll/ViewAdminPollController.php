<?php

namespace App\Http\Controllers\Poll;

use App\Comment;
use App\Exceptions\AlreadyExistsException;
use App\Exceptions\ConcurrentEditionException;
use App\Exceptions\ConcurrentVoteException;
use App\Exceptions\MomentAlreadyExistsException;
use App\Http\Controllers\Controller;
use App\Mail\SendPollNotification;
use App\Poll;
use App\Slot;
use App\Utils;
use App\Vote;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use stdClass;

class ViewAdminPollController extends Controller
{
    /**
     * @param Request $request
     * @param $adminPollId
     * @param null|string $action
     * @param int $parameter
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @internal param int $editingVoteId
     * @throws \Throwable
     */
    public function index(Request $request, $adminPollId, $action = '', $parameter = 0)
    {
        $poll = null;
        $editingVoteId = 0;

        $poll = Poll::where('admin_id', $adminPollId)->first();

        if (!$poll) {
            return response()->view('errors.error', [
                'title' => __('error.This poll doesn\'t exist !')
            ], 404);
        }

        if ($request->has('back')) {
            return redirect(Utils::getPollUrl($adminPollId, true));
        }

        if ($action == 'vote') {
            $editingVoteId = $parameter;
        }

        // -------------------------------
        // Update poll info
        // -------------------------------

        if ($request->has('update_poll_info')) {
            $updated = false;
            $field = in_array($request->input('update_poll_info'), ['title', 'admin_mail', 'description',
                'rules', 'expiration_date', 'name', 'hidden', 'removePassword', 'password'], true) ? $request->input('update_poll_info') : null;

            // Update the right poll field
            if ($field == 'title') {
                if ($request->has('title')) {
                    $poll->title = $request->input('title');
                    $updated = true;
                }
            } elseif ($field == 'admin_mail') {
                $validator = validator()->make($request->all(),[
                    'admin_mail' => 'required|email|max:128',
                ]);
                if (!$validator->fails()) {
                    $poll->admin_mail = $request->input('admin_mail');
                    $updated = true;
                }
            } elseif ($field == 'description') {
                if ($request->has('description')) {
                    $poll->description = $request->get('description');
                    $updated = true;
                }
            } elseif ($field == 'rules') {
                $rules = strip_tags($request->input('rules'));
                switch ($rules) {
                    case 0:
                        $poll->active = false;
                        $poll->editable = config('laradate.NOT_EDITABLE');
                        $updated = true;
                        break;
                    case 1:
                        $poll->active = true;
                        $poll->editable = config('laradate.NOT_EDITABLE');
                        $updated = true;
                        break;
                    case 2:
                        $poll->active = true;
                        $poll->editable = config('laradate.EDITABLE_BY_ALL');
                        $updated = true;
                        break;
                    case 3:
                        $poll->active = true;
                        $poll->editable = config('laradate.EDITABLE_BY_OWN');
                        $updated = true;
                        break;
                }
            } elseif ($field == 'expiration_date') {
                $expirationDate = DateTime::createFromFormat(__('date.datetime_parseformat'), $request->input('expiration_date'))->setTime(0, 0, 0);
                $expirationDate = $expirationDate->format('Y-m-d H:i:s');
                if ($expirationDate) {
                    $poll->end_date = $expirationDate;
                    $updated = true;
                }
            } elseif ($field == 'name') {
                if ($request->has('name')) {
                    $poll->admin_name = $request->input('name');
                    $updated = true;
                }
            } elseif ($field == 'hidden') {
                $hidden = $request->has('hidden');
                if ($hidden != $poll->hidden) {
                    $poll->hidden = $hidden;
                    $poll->results_publicly_visible = false;
                    $updated = true;
                }
            } elseif ($field == 'removePassword') {
                $removePassword = $request->has('removePassword') ? $request->input('removePassword') : false;
                if ($removePassword) {
                    $poll->results_publicly_visible = false;
                    $poll->password_hash = null;
                    $updated = true;
                }
            } elseif ($field == 'password') {
                $password = $request->has('password') ? $request->input('password') : null;
                $resultsPubliclyVisible = $request->has('resultsPubliclyVisible') ? $request->input('resultsPubliclyVisible') : false;
                if (!empty($password)) {
                    $poll->password_hash =  password_hash($password, PASSWORD_DEFAULT);
                    $updated = true;
                }
                if ($poll->password_hash == null || $poll->hidden == true){
                    $poll->results_publicly_visible = false;
                }
                if ($resultsPubliclyVisible != $poll->results_publicly_visible && $poll->password_hash != null && $poll->hidden == false) {
                    $poll->results_publicly_visible = $resultsPubliclyVisible;
                    $updated = true;
                }
            }

            // Update poll in database
            if ($updated && $poll->end_date > $poll->creation_date && $poll->save()) {
                session()->flash('success', __('adminpoll.Poll saved'));
                Mail::send(new SendPollNotification($poll, SendPollNotification::UPDATE_POLL));
            } else {
                session()->flash('danger', __('error.Failed to save poll'));
                $poll = Poll::find($poll->id);
            }
        }

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
                    $result = Vote::updateVote($poll, $editedVote, $name, $choices, $slotsHash);
                    if ($result) {
                        session()->flash('success', __('adminpoll.Vote updated'));
                    } else {
                        session()->flash('danger', __('error.Update vote failed'));
                    }
                } catch (ConcurrentEditionException $cee) {
                    session()->flash('danger', __('error.Poll has been updated before you vote'));
                } catch (ConcurrentVoteException $cve) {
                    session()->flash('danger', __('error.Your vote wasn\'t counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry.'));
                }
            }
        } elseif ($request->has('save')) { // Add a new vote
            $name = $request->input('name');
            $choices = Utils::filterArray($request->input('choices'), FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => config('laradate.CHOICE_REGEX')]]);
            $slotsHash = Utils::isValidMd5($request->input('control'));

            if (count($choices) != count($request->input('choices'))) {
                session()->flash('danger', __('error.There is a problem with your choices'));
            } else {
                // Add vote
                try {
                    $result = Vote::addVote($poll, $name, $choices, $slotsHash);
                    if ($result) {
                        session()->flash('success', __('adminpoll.Vote added'));
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

        // -------------------------------
        // Delete a vote
        // -------------------------------

        if ($action == 'delete_vote') {
            $voteId = $parameter;
            if ($voteId && Vote::where('poll_id', $poll->id)->where('id', $voteId)->delete()) {
                session()->flash('success', __('adminpoll.Vote deleted'));
            } else {
                session()->flash('danger', __('error.Failed to delete the vote!'));
            }
            return redirect(Utils::getPollUrl($adminPollId, true));
        }

        // -------------------------------
        // Remove all votes
        // -------------------------------

        if ($action == 'remove_all_votes' && !$request->has('confirm_remove_all_votes')) {
            return view('confirm.delete_votes', [
                'poll_id' => $poll->id,
                'admin_poll_id' => $adminPollId,
                'title' => __('generic.Poll') . ' - ' . $poll->title
                ]);
        }
        if ($action == 'remove_all_votes' && $request->has('confirm_remove_all_votes')) {
            if (Vote::where('poll_id', $poll->id)->delete()) {
                session()->flash('success', __('adminpoll.All votes deleted'));
            } else {
                session()->flash('danger', __('error.Failed to delete all votes'));
            }
            return redirect(Utils::getPollUrl($adminPollId, true));
        }

        // -------------------------------
        // Delete a comment
        // -------------------------------

        if ($action == 'delete_comment') {
            if (Comment::where('poll_id', $poll->id)->where('id', $parameter)->delete()) {
                session()->flash('success', __('adminpoll.Comment deleted'));
            } else {
                session()->flash('danger', __('error.Failed to delete the comment'));
            }
            return redirect(Utils::getPollUrl($adminPollId, true));
        }

        // -------------------------------
        // Remove all comments
        // -------------------------------

        if ($action == 'remove_all_comments' && !$request->has('confirm_remove_all_comments')) {
            return view('confirm.delete_comments', [
                'poll_id' => $poll->id,
                'admin_poll_id' => $adminPollId,
                'title' => __('generic.Poll') . ' - ' . $poll->title
            ]);
        }
        if ($action == 'remove_all_comments' && $request->has('confirm_remove_all_comments')) {
            if (Comment::where('poll_id', $poll->id)->delete()) {
                session()->flash('success', __('adminpoll.All comments deleted'));
            } else {
                session()->flash('danger', __('error.Failed to delete all comments'));
            }
            return redirect(Utils::getPollUrl($adminPollId, true));
        }

        // -------------------------------
        // Delete the entire poll
        // -------------------------------

        if ($action == 'delete_poll' && !$request->has('confirm_delete_poll')) {
            return view('confirm.delete_poll', [
                'poll_id' => $poll->id,
                'admin_poll_id' => $adminPollId,
                'title' => __('generic.Poll') . ' - ' . $poll->title
            ]);
        }
        if ($action == 'delete_poll' && $request->has('confirm_delete_poll')) {
            if ($poll->delete()) {
                session()->flash('success', __('adminpoll.Poll fully deleted'));
                Mail::send(new SendPollNotification($poll, SendPollNotification::DELETED_POLL));
            } else {
                session()->flash('danger', __('error.Failed to delete the poll'));
            }
            return view('confirm.poll_deleted', [
                'poll_id' => $poll->id,
                'admin_poll_id' => $adminPollId,
                'title' => __('generic.Poll') . ' - ' . $poll->title
            ]);
        }

        // -------------------------------
        // Delete a slot
        // -------------------------------

        if ($action == 'delete_column') {
            $column = Utils::base64url_decode($parameter);

            if ($poll->format === 'D') {
                $ex = explode('@', $column);

                $slot = new stdClass();
                $slot->title = $ex[0];
                $slot->moment = $ex[1];

                $result = Slot::deleteDateSlot($poll, $slot);
            } else {
                $result = Slot::deleteClassicSlot($poll, $column);
            }

            if ($result) {
                session()->flash('success', __('adminpoll.Column removed'));
            } else {
                session()->flash('danger', __('error.Failed to delete column'));
            }
            return redirect(Utils::getPollUrl($adminPollId, true));
        }

        // -------------------------------
        // Add a slot
        // -------------------------------

        if ($action == 'add_column' && !$request->has('confirm_add_column')) {
            return $this->exitDisplayingAddColumn($poll, $adminPollId);
        }

        if ($action == 'add_column' && $request->has('confirm_add_column')) {
            try {
                if (($poll->format === 'D' && empty($request->input('newdate')))
                    || ($poll->format === 'A' && empty($request->input('choice')))) {
                    session()->flash('danger', __('error.Can\'t create an empty column'));
                    return $this->exitDisplayingAddColumn($poll, $adminPollId);
                }
                if ($poll->format === 'D') {
                    $date = DateTime::createFromFormat(__('date.datetime_parseformat'), $request->input('newdate'))->setTime(0, 0, 0);
                    $time = $date->getTimestamp();
                    $newMoment = str_replace(',', '-', strip_tags($request->input('newmoment')));
                    Slot::addDateSlot($poll->id, $time, $newMoment);
                } else {
                    $newSlot = str_replace(',', '-', strip_tags($request->input('choice')));
                    Slot::addClassicSlot($poll->id, $newSlot);
                }

                session()->flash('success', __('adminpoll.Choice added'));
            } catch (MomentAlreadyExistsException $e) {
                session()->flash('danger', __('error.The column already exists'));
                return $this->exitDisplayingAddColumn($poll, $adminPollId);
            }
            return redirect(Utils::getPollUrl($adminPollId, true));
        }

        // Retrieve data
        $slots = Vote::allSlotsByPoll($poll);
        $votes = Vote::where('poll_id', $poll->id)->orderBy('id')->get();
        $comments = Comment::where('poll_id', $poll->id)->orderBy('id')->get();

        // Assign data to template
        return view('poll', [
            'poll_id' => $poll->id,
            'admin_poll_id' => $adminPollId,
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
            'admin' => true,
            'hidden' => false,
            'accessGranted' => true,
            'resultPubliclyVisible' => true,
            'editedVoteUniqueId' => '',
            'editableVoteHtml' => null,
        ]);
    }
    
    private function exitDisplayingAddColumn($poll, $admin_poll_id) {
        return view('admin.add_column', [
            'poll_id' => $poll->id,
            'admin_poll_id' => $admin_poll_id,
            'format' => $poll->format,
            'title' => __('generic.Poll') . ' - ' . $poll->title
        ]);
    }
}
