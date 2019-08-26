<?php

namespace App\Console\Commands;

use App\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ImageDownload extends AbstractCommand
{

    protected $signature = 'image:download {--all} {--skip-existing}';

    protected $description = 'Downloads all images from LAKE IIIF';

    private $sleep = 0;

    public function handle()
    {
        // Only get images that haven't been downloaded yet
        $images = $this->option('all') ? Image::query() : Image::whereNull('image_downloaded_at');

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
                if ($this->option('skip-existing'))
                {
                    $this->warn("{$image->id} - already exists – skipping!");
                    continue;
                }

                $image->image_attempted_at = null;
                $image->image_downloaded_at = null;
                $image->image_cache_hit = null;
                $image->save();

                Storage::delete($file);

                $this->warn("{$image->id} - already exists – removed!");
            }

            $image->image_attempted_at = Carbon::now();
            $image->save();

            try
            {
                $contents = $this->fetch($url, $headers);
                Storage::put($file, $contents);

                $image->image_downloaded_at = Carbon::now();
                $image->image_cache_hit = in_array('x-cache: hit from cloudfront', array_map('strtolower', $headers));
                $image->save();

                $this->info("{$image->id} - downloaded");

                // Give the IIIF server a rest
                if (!$image->image_cache_hit)
                {
                    usleep($this->sleep * 1000000);
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
