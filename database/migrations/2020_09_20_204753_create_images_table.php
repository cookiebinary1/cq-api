<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->text('data');
            $table->foreignId('created_by_id')->nullable();
            $table->timestamps();
        });

        Schema::table('creators', function (Blueprint $table){
            $table->foreignId('image_id')->nullable()->after('country_id');
        });

        Schema::table('users', function (Blueprint $table){
            $table->foreignId('image_id')->nullable()->after('country_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
