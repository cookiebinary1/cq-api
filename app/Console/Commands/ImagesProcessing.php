<?php

namespace App\Console\Commands;

use App\Jobs\ImageProcess;
use App\Models\Image;
use Illuminate\Console\Command;

class ImagesProcessing extends Command
{
    protected $signature = 'images:process {--force}';
    protected $description = 'Process images and download from original services to our CDN.';

    /**
     * ProcessImages constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        if ($force = $this->option('force')) {
            $imageBuilder = Image::cursor();
        } else {
            $imageBuilder = Image::unprocessed()->cursor();
        }

        $i = 0;
        /** @var Image $image */
        foreach ($imageBuilder as $image) {
            $i++;
            ImageProcess::dispatch($image, $force)->delay(now()->addSeconds($i));
            $this->info("image dispatch to process: " . $image->url);
        }

        $this->info("\nTotal to process: " . Image::unprocessed()->count() . "\n");

        return 0;
    }
}
