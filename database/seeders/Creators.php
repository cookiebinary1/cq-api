<?php

namespace Database\Seeders;

use App\Models\Creator;
use DB;
use Illuminate\Database\Seeder;
use Str;

class Creators extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("delete from creators");
        $faker = \Faker\Factory::create();


        for ($i = 0; $i < 500; $i++) {
            $creator = Creator::create([
                'name' => $faker->name,
                'description' => $faker->email,
                'status' => 'active',
            ]);
            echo $creator->name . " - " . $creator->description . "\n";
        }
    }
}
