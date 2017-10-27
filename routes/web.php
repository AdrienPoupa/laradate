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

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\RegisterController@register');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/', 'HomeController@index')->name('home');
Route::get('create/{type}', 'Poll\CreatePollController@index')->name('create')->where(['type' => '^(date|classic)$']);
Route::post('create/{type}', 'Poll\CreatePollController@store')->where(['type' => '^(date|classic)$']);

Route::any('create/classic/2', 'Poll\CreateClassicPollController@index')->name('create-classic');
Route::any('create/date/2', 'Poll\CreateDatePollController@index')->name('create-date');

Route::get('poll/find', 'Poll\FindPollController@index');
Route::post('poll/find', 'Poll\FindPollController@post');

Route::any('poll/{poll_id}', 'Poll\ViewPollController@index')->name('view-poll')->where(['poll_id' => '^[a-zA-Z0-9-]*$']);
Route::get('poll/{poll_id}/csv/{admin?}', 'Poll\CreatePollCsvController@index')->name('csv')->where(['poll_id' => '^[a-zA-Z0-9-]*$'])->where(['admin' => '^[a-zA-Z0-9-]{24}$']);
Route::any('poll/{poll_id}/vote/{vote}', 'Poll\ViewPollController@index')->name('edit-vote')->where(['poll_id' => '^[a-zA-Z0-9-]*$'])->where(['vote' => '^[a-zA-Z0-9-]*$']);
Route::post('poll/{poll_id}/send_edit_link/{editedVoteUniqueId}', 'Poll\SendLinkController@index')->name('edit-vote')->where(['poll_id' => '^[a-zA-Z0-9-]*$'])->where(['editedVoteUniqueId' => '^[a-zA-Z0-9-]*$']);
Route::post('poll/{poll_id}/comment', 'Poll\CommentPollController@post')->name('post-comment')->where(['poll_id' => '^[a-zA-Z0-9-]*$']);

Route::any('poll/{poll_id}/admin', 'Poll\ViewAdminPollController@index')->name('view-poll')->where(['poll_id' => '^[a-zA-Z0-9-]{24}$']);
Route::any('poll/{poll_id}/admin/{action}/{parameter?}', 'Poll\ViewAdminPollController@index')->name('view-poll')->where(['poll_id' => '^[a-zA-Z0-9-]{24}$'])->where(['action' => '^[a-zA-Z0-9-_]*$'])->where(['parameter' => '^[a-zA-Z0-9-]*$']);

Route::get('admin', function() {
   return view('admin.index');
});

Route::any('admin/polls', 'Admin\AdminPollController@index')->name('admin-polls');
Route::get('admin/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('admin/purge', 'Admin\AdminPurgePollController@index');
Route::post('admin/purge', 'Admin\AdminPurgePollController@post');