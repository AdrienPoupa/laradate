<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile', ['title' => __('generic.Profile')]);
    }

    public function post(UpdateProfile $request)
    {
        $user = Auth::user();
        $user->email = $request->input('mail');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        session()->flash('success', __('generic.Profile updated'));

        return redirect('profile');
    }
}
