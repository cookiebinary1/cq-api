<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Slugs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        Schema::table('categories', function (Blueprint $table) {
//            $table->string('slug')->nullable()->after('name');
//        });

        Schema::table('collabs', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });

        Schema::table('creators', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
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
