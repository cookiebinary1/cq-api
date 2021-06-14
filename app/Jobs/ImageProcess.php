<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ImageProcess
 * @package App\Jobs
 * @author Cookie
 */
class ImageProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Image $image;
    protected bool $force = false;

    /**
     * ImageProcess constructor.
     * @param Image $image
     * @param       $force
     */
    public function __construct(Image $image, bool $force = false)
    {
        $this->image = $image;
        $this->force = $force;
    }

    /**
     *
     */
    public function handle()
    {
        $this->image->process($this->force);
    }
}
