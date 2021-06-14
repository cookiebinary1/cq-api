<?php

namespace App\Console\Commands;

use App\Exceptions\ErrorException;
use App\Models\Collab;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Class LikeFaker
 * @package App\Console\Commands
 * @author Cookie
 */
class LikeFaker extends Command
{
    protected $signature = 'likes:fake';
    protected $description = 'Command description';

    /**
     * LikeFaker constructor.
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
        // get array of ids of all users
        $userIds = User::pluck('id');

        /** @var Collab $collab */
        foreach (Collab::cursor() as $collab) {
            $likes = round(pow(rand(0, 500), 3) / 10000);
            $likes = $likes > $userIds->count() ? $userIds->count() : $likes;
            $this->info("Try to likes Collab ID:{$collab->id} with $likes likes");

            $bar = $this->output->createProgressBar($likes);
            $bar->start();

            foreach (range(0, $likes) as $i) {
                try {
                    $collab->addLike($userId = $userIds->random());
                } catch (ErrorException $exception) {
                    // do nothing ~ whatever
                }

                $bar->advance(1);
            }
            $bar->finish();
            $this->info("\n\n");
        }

        return 0;
    }
}
