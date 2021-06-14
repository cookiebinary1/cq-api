<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TotalUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("categories", function (Blueprint $table) {
            $table->boolean('generic')->default(false)->after('description');
        });

        Schema::table("collabs", function (Blueprint $table) {
            $table->string('status')->default('new')->after('creator2_id');
            $table->text('description')->nullable()->after('creator2_id');
        });

        Schema::table("creator_info", function (Blueprint $table) {
           $table->boolean('visible')->default(true)->after('value');
           $table->boolean('generic')->default(false)->after('value');
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
