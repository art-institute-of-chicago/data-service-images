<?php

namespace App\Console\Commands;

use App\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class InfoDownload extends AbstractCommand
{

    protected $signature = 'info:download {--all} {--skip-existing}';

    protected $description = 'Downloads info.json files from IIIF';

    private $sleep = 0;

    public function handle()
    {
        $images = $this->option('all') ? Image::query() : $images = Image::whereNull('info_downloaded_at');

        if (!$this->confirm($images->count() . ' info files will be downloaded. Proceed?'))
        {
            return;
        }

        foreach ($images->cursor(['id']) as $image)
        {
            $file = "info/{$image->id}.json";
            $url = env('IIIF_URL') . "/{$image->id}/info.json";

            if (Storage::exists($file))
            {
                if ($this->option('skip-existing'))
                {
                    $this->warn("{$image->id} - already exists – skipping!");
                    continue;
                }

                $image->info_attempted_at = null;
                $image->info_downloaded_at = null;
                $image->info_cache_hit = null;
                $image->save();

                Storage::delete($file);

                $this->warn("{$image->id} - already exists – removed!");
            }

            $image->info_attempted_at = Carbon::now();
            $image->save();

            try
            {
                $contents = $this->fetch($url, $headers);
                Storage::put($file, $contents);

                $image->info_downloaded_at = Carbon::now();
                $image->info_cache_hit = in_array('x-cache: hit from cloudfront', array_map('strtolower', $headers));
                $image->save();

                $this->info("{$image->id} - downloaded");

                // Give the IIIF server a rest
                if (!$image->info_cache_hit)
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
