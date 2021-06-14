<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Asdfasdf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('source_creator', 'creator_source');
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
