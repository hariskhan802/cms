<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostCategoryRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_category_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->nullable();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->unsignedBigInteger('cat_id')->nullable();
            $table->foreign('cat_id')->references('id')->on('categories')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_category_relations');
    }
}
