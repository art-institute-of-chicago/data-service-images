<?php

namespace App\Console\Commands;

use App\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ImageDownload extends AbstractCommand
{

    protected $signature = 'image:download';

    protected $description = 'Downloads all images from LAKE IIIF';

    public function handle()
    {
        $images = Image::whereNull('image_attempted_at');

        if (!$this->confirm($images->count() . ' images will be downloaded. Proceed?'))
        {
            return;
        }

        foreach ($images->cursor(['id']) as $image)
        {
            $file = "images/{$image->id}.jpg";
            $url = env('IIIF_URL') . "/{$image->id}/full/843,/0/default.jpg";

            if (Storage::exists($file))
            {
                $this->warn("{$image->id} - already exists");
                continue;
            }

            $image->image_attempted_at = Carbon::now();
            $image->save();

            try
            {
                $contents = $this->fetch($url, $headers);
                Storage::put($file, $contents);

                $image->image_downloaded_at = Carbon::now();
                $image->save();

                $this->info("{$image->id} - downloaded");

                // Give the IIIF server a rest
                if (!in_array('X-Cache: Hit from cloudfront', $headers))
                {
                    usleep(500000); // Half a second
                }
            }
            catch (\Exception $e)
            {
                // TODO: Avoid catching non-HTTP exceptions?
                $this->warn("{$image->id} - not found - {$url}");

                // Update the attempt date
                $image->save();

                continue;
            }
        }
    }

}