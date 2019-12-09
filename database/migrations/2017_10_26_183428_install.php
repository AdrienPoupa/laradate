<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Install extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Fix key too long bug https://github.com/laravel/framework/issues/17508
        Schema::defaultStringLength(191);

        Schema::create('polls', function (Blueprint $table) {
            $table->string('id', 16);
            $table->char('admin_id', 24);
            $table->text('title');
            $table->text('description')->nullable();;
            $table->string('admin_name', 64)->nullable();
            $table->string('admin_mail', 128)->nullable();
            $table->timestamp('creation_date')->useCurrent();
            $table->timestamp('end_date')->useCurrent();
            $table->string('format', 1)->nullable();
            $table->tinyInteger('editable')->default(0);
            $table->tinyInteger('receiveNewVotes')->default(0);
            $table->tinyInteger('receiveNewComments')->default(0);
            $table->tinyInteger('results_publicly_visible')->nullable();
            $table->tinyInteger('active')->default(1);
            $table->tinyInteger('hidden')->default(0);
            $table->tinyInteger('value_max')->default(0);
            $table->string('password_hash', 255)->nullable();
            $table->primary('id');
            $table->unique('id');
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uniqId', 16);
            $table->string('poll_id', 64);
            $table->string('name', 64);
            $table->text('choices');
            $table->unique('uniqId');
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('poll_id', 64);
            $table->string('name', 64)->nullable();
            $table->text('comment');
            $table->timestamp('date')->useCurrent();
        });

        Schema::create('slots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('poll_id', 64);
            $table->text('title')->nullable();
            $table->text('moments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
        Schema::dropIfExists('votes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('slots');
    }
}
