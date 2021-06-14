<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CollabTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("collabs", function (Blueprint $table) {
            $table->string("title")->after("creator2_id")->default("This is collab title");
            $table->dropForeign('collabs_creator1_id_foreign');
            $table->dropForeign('collabs_creator2_id_foreign');
            $table->dropUnique("collabs_creator1_id_creator2_id_unique");
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
