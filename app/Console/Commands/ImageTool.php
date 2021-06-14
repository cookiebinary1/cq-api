<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\Input;

class ImageTool extends Command
{
    protected $signature = 'image:tool {tool} {--user-id= : Image by user} {--image-id= : Image by self ID}';
    protected $description = '
        fix_data_field - just json decode and encode cdn data (do no trigger this)
        revert_cdn_to_origin - it\'s save - just reset CDN values, so make ready to reprocess again
        reload - force reload/reprocess one image
    ';

    /**
     * ImageTool constructor.
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
        switch ($tool = $this->argument('tool')) {

            case 'fix_data_field':
                /** @var Image $image */
                foreach (Image::cursor() as $image) {
                    if (is_string($image->data)) {
                        $image->data = json_decode($image->data) ?? $image->data;
                        $image->save();
                    }
                    $this->info("processed: " . $image->url);
                }
                break;

            case 'revert_cdn_to_origin':
                /** @var Image $image */
                foreach (Image::processed()->creators()->cursor() as $image) {
                    $image->revertToOriginal();
                    $this->info("processed: " . $image->url);
                }
                break;

            case 'reload':
                /** @var Image $image */
                if ($imageId = $this->option('image-id')) {
                    $image = Image::find($imageId);
                }
                if ($userId = $this->option('user-id')) {
                    $image = User::find($userId)->image;
                }

                $image
                    ->revertToOriginal()
                    ->process(true);

                $this->info("processed: " . $image->url);

                break;

            case 'imagekit_clear':
                $deleted = 0;
                $skip = 0;
                do {
                    $data = imagekit_api()->listFiles([
                        "path"  => app()->environment(),
                        "skip"  => $skip,
                        "limit" => 200,
                    ]);

                    foreach ($data->success as $item) {
                        echo $item->url . "...  ";
                        if (Image::whereUrl($item->url)->exists()) {
                            echo "ok\n";
                        } else {
                            imagekit_api()->deleteFile($item->fileId);
                            echo "not recognized - DELETE\n";
                            $deleted++;
                        }
                        $skip++;
                    }

                } while (count($data->success));

                $this->info("DELETED TOTAL: $deleted");
                break;

            default:
                $this->error("Tool '$tool' undefined!");
        }

        return 0;
    }
}
