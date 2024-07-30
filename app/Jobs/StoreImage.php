<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Filesystem\Factory;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use App\Components\Orders\Contracts\ImagePathsInterface;

class StoreImage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ImagePathsInterface $paths;

    public function __construct(ImagePathsInterface $paths)
    {
        $this->paths = $paths;
    }

    public function handle(ConfigContract $conf, Factory $storage)
    {
        $originalHeight = $conf->get('image.original_height');
        $thumbHeight = $conf->get('image.thumb_height');
        $extension = $conf->get('image.file_extension');

        try {
            $disk = $storage->disk($conf->get('image.disk'));
            $content = file_get_contents($this->paths->getBlobLink());
            $img = Image::read($content);

//            $img = $img->flop();
            $imgOriginalResized = $img->scale(null, $originalHeight);
            $imgOriginalConverted = $imgOriginalResized->encodeByExtension($extension);

            $disk->put(
                $this->paths->getFileFullPath(),
                $imgOriginalConverted->toFilePointer()
            );

            $imgThumbResized = $img->scale(null, $thumbHeight);
            $imgThumbConverted = $imgThumbResized->encodeByExtension($extension);
            $disk->put(
                $this->paths->getFileThumbPath(),
                $imgThumbConverted->toFilePointer()
            );

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return;
        } finally {
            $storage->disk($conf->get('image.disk'))->delete($this->paths->getBlobPath());
        }
    }
}