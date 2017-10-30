<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfile extends FormRequest
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
            'mail' => 'sometimes|email|max:128',
            'password' => 'sometimes|min:6|max:64',
            'password_repeat' => 'required_with:password,|max:64|same:password',
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
            'mail.email' => __('error.The address is not correct! You should enter a valid email address (like r.stallman@outlook.com) in order to receive the link to your poll.'),
            'password_repeat.same' => __('error.Passwords do not match'),
        ];
    }
}
