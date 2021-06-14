<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeignKeys2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("categories", function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_id')->nullable()->change();
            $table->foreign('created_by_id')->references('id')->on('users');
        });

        Schema::table("category_creator", function (Blueprint $table) {
            $table->foreign('creator_id')->references('id')->on('creators')->cascadeOnDelete();
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
        });

        Schema::table("collabs", function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('creator1_id')->references('id')->on('creators');
            $table->foreign('creator2_id')->references('id')->on('creators');
        });

        Schema::table("creator_info", function (Blueprint $table) {
            $table->foreign('creator_id')->references('id')->on('creators')->cascadeOnDelete();
        });

        Schema::table("creators", function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_id')->nullable()->change();
            $table->foreign('created_by_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('image_id')->references('id')->on('images');
        });

        Schema::table("images", function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_id')->nullable()->change();
            $table->foreign('created_by_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table("likes", function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('collab_id')->references('id')->on('collabs')->cascadeOnDelete();
        });

        Schema::table("users", function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->unsignedBigInteger('image_id')->nullable()->change();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('image_id')->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
