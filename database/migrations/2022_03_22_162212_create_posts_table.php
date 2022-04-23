<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->text('post_title');
            $table->text('post_excerpt');
            $table->string('post_name')->unique();
            $table->longText('post_content');
            $table->longText('post_content_filtered');
            $table->string('comment_status')->default('open');
            $table->bigInteger('comment_count')->default('0');
            $table->string('post_status')->default('publish');
            $table->string('post_password');
            $table->string('post_type')->default('post');
            $table->string('post_mime_type');
            $table->unsignedBigInteger('post_author')->default('0');
            $table->text('pinged');
            $table->text('to_ping');
            $table->string('ping_status')->default('open');
            $table->string('guid');
            $table->unsignedBigInteger('post_parent')->default('0');
            $table->integer('menu_order')->default('0');
            $table->timestamp('post_date')->useCurrent();
            $table->timestamp('post_date_gmt')->useCurrent();
            $table->timestamp('post_modified')->nullable()->useCurrentOnUpdate();
            $table->timestamp('post_modified_gmt')->nullable()->useCurrentOnUpdate();
            
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
