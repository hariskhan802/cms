<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('comment_ID');
            $table->unsignedBigInteger('comment_post_ID')->default('0');
            $table->tinyText('comment_author');
            $table->string('comment_author_email')->index();
            $table->string('comment_author_url');
            $table->string('comment_author_IP');
            $table->timestamp('comment_date')->useCurrent();
            $table->timestamp('comment_date_gmt')->useCurrent()->index();
            $table->text('comment_content');
            $table->integer('comment_karma')->default('0');
            $table->string('comment_approved')->default('1')->index();
            $table->string('comment_agent');
            $table->string('comment_type')->default('comment');
            $table->unsignedBigInteger('comment_parent')->default('0');
            $table->unsignedBigInteger('user_id')->default('0');

            // $table->index(['comment_approved', 'comment_date_gmt', 'comment_author_email']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
