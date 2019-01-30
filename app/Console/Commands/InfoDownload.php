<?php

namespace App\Console\Commands;

use App\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InfoDownload extends AbstractCommand
{

    protected $signature = 'info:download';

    protected $description = 'Downloads info.json files from IIIF';

    public function handle()
    {
        // Only target images that haven't been attempted yet
        $images = Image::whereNull('info_attempted_at');

        // Only target images that don't have dimensions yet
        $images = $images->where(function($query) {
            $query->whereNull('width')->orWhereNull('height');
        });

        foreach ($images->cursor(['id']) as $image)
        {
            $file = "info/{$image->id}.json";
            $url = env('IIIF_URL') . "/{$image->id}/info.json";

            if (Storage::exists($file))
            {
                $this->warn("{$image->id} - already exists");
                continue;
            }

            $image->info_attempted_at = Carbon::now();
            $image->save();

            try
            {
                $contents = $this->fetch($url);
                Storage::put($file, $contents);

                $image->info_downloaded_at = Carbon::now();
                $image->save();

                $this->info("{$image->id} - downloaded");

                usleep(500000); // Half a second
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
