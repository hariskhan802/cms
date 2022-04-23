<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->bigIncrements('link_id');
            $table->string('link_url');
            $table->string('link_name');
            $table->string('link_image');
            $table->string('link_target');
            $table->string('link_description');
            $table->string('link_visible')->default('Y')->index();
            $table->unsignedBigInteger('link_owner')->default('1');
            $table->integer('link_rating')->default('0');
            $table->timestamp('link_updated')->nullable()->useCurrentOnUpdate();
            $table->string('link_rel');
            $table->mediumText('link_notes');
            $table->string('link_rss');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
}
