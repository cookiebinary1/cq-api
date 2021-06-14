<?php

use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SourcesUp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->string('priority_field')->after('description');
        });

        Source::create([
            'name'        => 'mixcloud',
            'description' => 'Mixcloud user',
            'priority_field'=>'follower_count',
        ]);

        $data = [
            'youtube'    => 'subscriberCount',
            'soundcloud' => 'followers',
            'twitch'     => 'followers',
            'spotify'    => 'followers',
            'mixcloud'   => 'follower_count',
        ];

        foreach ($data as $name => $priorityField)
            Source::whereName($name)->update(['priority_field' => $priorityField]);
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
