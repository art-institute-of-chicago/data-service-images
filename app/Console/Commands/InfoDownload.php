<?php

namespace App\Console\Commands;

use App\Image;

use Illuminate\Support\Facades\Storage;

class InfoDownload extends AbstractCommand
{

    protected $signature = 'info:download';

    protected $description = 'Downloads info.json files from IIIF';

    public function handle()
    {
        // Only target images that don't have dimensions yet
        $images = Image::whereNull('width')->orWhereNull('height');

        foreach ($images->cursor(['id']) as $image)
        {
            $file = "info/{$image->id}.json";
            $url = env('IIIF_URL') . "/{$image->id}/info.json";

            if (Storage::exists($file))
            {
                $this->warn("{$image->id} - already exists");
                continue;
            }

            try {
                $contents = $this->fetch($url);
                Storage::put($file, $contents);
                $this->info("{$image->id} - downloaded");
                sleep(1);
            }
            catch (\Exception $e) {
                // TODO: Avoid catching non-HTTP exceptions?
                $this->warn("{$image->id} - not found - {$url}");
                continue;
            }
        }
    }

}
