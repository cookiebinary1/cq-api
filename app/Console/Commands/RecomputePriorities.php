<?php

namespace App\Console\Commands;

use App\Models\Creator;
use Illuminate\Console\Command;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class RecomputePriorities
 * @package App\Console\Commands
 * @author Cookie
 */
class RecomputePriorities extends Command
{
    protected $signature = 'recompute:priorities';
    protected $description = 'Command description (todo)';

    /**
     * RecomputePriorities constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     * @throws InvalidArgumentException
     */
    public function handle(): int
    {
        /** @var Creator $creator */
        foreach (Creator::cursor() as $creator) {
            $this->info("processing " . $creator->name . "...");
            $creator->recomputePriority();
            $this->info("OK!");
        }

        return 0;
    }
}
