<?php

namespace App\Console\Commands;

use App\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InfoDownload extends AbstractCommand
{

    protected $signature = 'info:download {--all} {--sleep= : Seconds to sleep between requests}';

    protected $description = 'Downloads info.json files from IIIF';

    private $sleep;

    public function handle()
    {
        $this->sleep = is_numeric($this->option('sleep')) ? floatval($this->option('sleep')) : 0.5;

        $images = Image::query();

        if (!$this->option('all'))
        {
            // Only target images that haven't been attempted yet
            $images = $images->whereNull('info_attempted_at');

            // Only target images that don't have dimensions yet
            $images = $images->where(function($query) {
                $query->whereNull('width')->orWhereNull('height');
            });
        }

        if (!$this->confirm($images->count() . ' info files will be downloaded. Proceed?'))
        {
            return;
        }

        foreach ($images->cursor(['id']) as $image)
        {
            $file = "info/{$image->id}.json";
            $url = env('IIIF_URL') . "/{$image->id}/info.json";

            if (!$this->option('all') && Storage::exists($file))
            {
                $this->warn("{$image->id} - already exists");
                continue;
            }

            $image->info_attempted_at = Carbon::now();
            $image->save();

            try
            {
                $contents = $this->fetch($url, $headers);
                Storage::put($file, $contents);

                $image->info_downloaded_at = Carbon::now();
                $image->save();

                $this->info("{$image->id} - downloaded");

                // Give the IIIF server a rest
                if (!in_array('X-Cache: Hit from cloudfront', $headers))
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
