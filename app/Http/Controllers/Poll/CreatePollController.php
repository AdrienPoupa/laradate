<?php

namespace App\Http\Controllers\Poll;

use App\Form;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePoll;

class CreatePollController extends Controller
{
    public function store(StorePoll $request, $type)
    {
        $form = new Form();
        $form->type = $type;
        $form->title = $request->input('title');
        $form->id = $request->input('customized_url');
        $form->use_customized_url = $request->input('use_customized_url');
        $form->admin_name = $request->input('name');
        $form->admin_mail = $request->input('mail');
        $form->description = $request->input('description');
        $form->editable = $request->input('editable');
        $form->receiveNewVotes = $request->input('receiveNewVotes');
        $form->receiveNewComments = $request->input('receiveNewComments');
        $form->hidden = $request->input('hidden');
        $form->use_password = $request->input('use_password');
        $form->results_publicly_visible = $request->input('results_publicly_visible');
        $form->useValueMax = $request->filled('use_value_max') ? true : false;
        $form->valueMax = $form->useValueMax ? $request->input('value_max') : 0;

        // If no errors, we hash the password if needed
        if ($form->use_password) {
            $form->password_hash = password_hash($request->input('password'), PASSWORD_DEFAULT);
        } else {
            $form->password_hash = null;
            $form->results_publicly_visible = null;
        }

        session()->put('form', $form);
        session()->save();

        if ($type == 'date') {
            return redirect('create/date/2');
        }

        // Classic
        return redirect('create/classic/2');
    }

    public function index($type)
    {
        $useRemoteUser = config('laradate.USE_REMOTE_USER') && isset($_SERVER['REMOTE_USER']);

        return view('create.step_1', [
            'title' => __('step_1.Poll creation (1 on 3)'),
            'useRemoteUser' => $useRemoteUser,
            'use_smtp' => config('laradate.use_smtp'),
            'type' => $type
        ]);
    }
}
