<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\Category;

class TotalUpdate2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = [
            [
                'name'        => 'youtube',
                'description' => 'Youtube Channel',
            ],
            [
                'name'        => 'soundcloud',
                'description' => 'Soundcloud artist',
            ],
            [
                'name'        => 'spotify',
                'description' => 'Spotify artist',
            ],
        ];

        Schema::table('categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        foreach ($data as $item) {
            $category = Category::create($item);
            $category->generic = true;
            $category->save();
        }
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
