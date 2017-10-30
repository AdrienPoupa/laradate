<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePoll extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'use_customized_url' => 'sometimes',
            'customized_url' => 'required_if:use_customized_url,on|nullable|unique:polls,id|max:64|regex:/^[a-z0-9-]*$/i',
            'name' => 'required|max:64',
            'mail' => 'required|email|max:128',
            'editable' => 'required|integer|between:0,2',
            'receiveNewVotes' => 'sometimes',
            'receiveNewComments' => 'sometimes',
            'hidden' => 'sometimes',
            'use_password' => 'sometimes',
            'password' => 'required_if:use_password,on|max:64',
            'password_repeat' => 'required_if:use_password,1|max:64|same:password',
            'results_publicly_visible' => 'sometimes',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' =>  __('error.Enter a title'),
            'mail.email' => __('error.The address is not correct! You should enter a valid email address (like r.stallman@outlook.com) in order to receive the link to your poll.'),
            'password.required_if' => __('error.Password is empty'),
            'password_repeat.same' => __('error.Passwords do not match'),
        ];
    }
}
