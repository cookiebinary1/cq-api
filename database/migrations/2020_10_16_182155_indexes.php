<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Indexes extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_creator', function (Blueprint $table){
            $table->unique(['creator_id', 'category_id']);
        });
        Schema::table('collabs', function (Blueprint $table){
            $table->unique(['creator1_id', 'creator2_id']);
            $table->index('user_id');
        });
        Schema::table('countries', function (Blueprint $table){
            $table->unique('num_code');
        });
        Schema::table('creator_info', function (Blueprint $table){
            $table->index(['creator_id']);
        });
        Schema::table('creators', function (Blueprint $table){
            $table->index(['country_id', 'image_id', 'name', 'priority']);
        });
        Schema::table('likes', function (Blueprint $table){
            $table->unique(['collab_id', 'user_id']);
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
