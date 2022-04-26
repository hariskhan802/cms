<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('user_login');
            $table->string('user_pass');
            $table->string('user_nicename');
            $table->string('user_email');
            $table->string('user_url');
            $table->timestamp('user_registered');
            $table->string('user_activation_key');
            $table->integer('user_status')->default('0');
            $table->string('display_name');

            $table->integer('is_super_admin')->default('0');
            $table->rememberToken();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
