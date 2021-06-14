<?php

namespace App\Console\Commands;

use App\Models\User;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

/**
 * Class UsersFaker
 * @package App\Console\Commands
 * @author Cookie
 */
class UsersFaker extends Command
{
    protected $signature = 'users:fake {--users=}';
    protected $description = 'Generate fake users, if not explicitly said, default number is 100';

    /**
     * UsersFaker constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle()
    {
        $faker = Factory::create();

        for ($i = 0; $i < ($this->option('users') ?? 100); $i++) {
            try {
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'password' => 'test123',
                ]);
                $this->info("User created: " . $user->name . " (" . $user->email . ")");
            } catch (QueryException $exception) {
                // do nothing ;)
            }
        }

        return 0;
    }
}
