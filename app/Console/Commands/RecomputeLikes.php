<?php

namespace App\Console\Commands;

use App\Models\Collab;
use Illuminate\Console\Command;

/**
 * Class RecomputeLikes
 * @package App\Console\Commands
 * @author Cookie
 */
class RecomputeLikes extends Command
{
    protected $signature = 'likes:recompute';
    protected $description = 'Recompute likes on each collab';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ($bar = $this->output->createProgressBar(Collab::count()))->start();

        /** @var Collab $collab */
        foreach (Collab::cursor() as $collab) {
            $collab->recomputeLikes();
            $bar->advance();
        }

        $this->info("\nDone.");
        return 0;
    }
}
