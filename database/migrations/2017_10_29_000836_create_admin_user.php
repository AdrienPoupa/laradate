<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $admin = new \App\User();
        $admin->name = 'Admin';
        $admin->email = config('laradate.ADMIN_MAIL');
        $admin->password = bcrypt(config('laradate.ADMIN_MAIL'));
        $admin->is_admin = 1;
        $admin->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $admin = \App\User::where('is_admin', 1)->first();
        $admin->delete();
    }
}
