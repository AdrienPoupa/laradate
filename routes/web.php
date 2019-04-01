<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');

// Authentication
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset.
Route::group(['prefix' => 'password'], function () {
    Route::get('reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('reset', 'Auth\ResetPasswordController@reset');
});

// Create a poll
Route::group(['prefix' => 'create'], function () {
    Route::get('{type}', 'Poll\CreatePollController@index')->name('create')->where(['type' => '^(date|classic)$']);
    Route::post('{type}', 'Poll\CreatePollController@store')->where(['type' => '^(date|classic)$']);

    Route::any('classic/2', 'Poll\CreateClassicPollController@index')->name('create-classic');
    Route::any('date/2', 'Poll\CreateDatePollController@index')->name('create-date');
});

// Display and admin a poll
Route::group(['prefix' => 'poll'], function () {
    Route::get('find', 'Poll\FindPollController@index');
    Route::post('find', 'Poll\FindPollController@post');

    Route::any('{poll}', 'Poll\ViewPollController@index')->name('view-poll')->where(['pollId' => config('laradate.REGEX_POLL_ROUTE')]);
    Route::get('{poll}/csv/{admin?}', 'Poll\CreatePollCsvController@index')->name('csv')->where(['pollId' => config('laradate.REGEX_POLL_ROUTE')])->where(['admin' => config('laradate.REGEX_POLL_ADMIN_ROUTE')]);
    Route::any('{poll}/vote/{vote}', 'Poll\ViewPollController@index')->name('edit-vote')->where(['pollId' => config('laradate.REGEX_POLL_ROUTE')])->where(['vote' => config('laradate.REGEX_POLL_ROUTE')]);
    Route::post('{poll}/send_edit_link/{editedVoteUniqueId}', 'Poll\SendLinkController@index')->name('edit-vote')->where(['pollId' => config('laradate.REGEX_POLL_ROUTE')])->where(['editedVoteUniqueId' => config('laradate.REGEX_POLL_ROUTE')]);
    Route::post('{poll}/comment', 'Poll\CommentPollController@post')->name('post-comment')->where(['pollId' => config('laradate.REGEX_POLL_ROUTE')]);

    Route::any('{poll}/admin', 'Poll\ViewAdminPollController@index')->name('view-poll')->where(['pollId' => config('laradate.REGEX_POLL_ADMIN_ROUTE')]);
    Route::any('{poll}/admin/{action}/{parameter?}', 'Poll\ViewAdminPollController@index')->name('view-poll')->where(['pollId' => config('laradate.REGEX_POLL_ADMIN_ROUTE')])->where(['action' => '^[a-zA-Z0-9-_]*$'])->where(['parameter' => config('laradate.REGEX_POLL_ROUTE')]);
});

// Admin panel
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', function() {
        return view('admin.index', ['title' => __('admin.Administration')]);
    });
    Route::get('polls', 'Admin\AdminPollController@index')->name('admin-polls');
    Route::post('polls', 'Admin\AdminPollController@index')->name('admin-polls');
    Route::delete('polls/{poll}', 'Admin\AdminPollController@destroy')->name('admin-polls-delete');
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('purge', 'Admin\AdminPurgePollController@index');
    Route::post('purge', 'Admin\AdminPurgePollController@post');
});

Route::get('profile', 'ProfileController@index')->middleware('auth');
Route::post('profile', 'ProfileController@post')->middleware('auth');
