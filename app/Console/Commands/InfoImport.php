<?php

namespace App\Console\Commands;

use App\Image;

use Illuminate\Support\Facades\Storage;

class InfoImport extends AbstractCommand
{

    protected $signature = 'info:import {--all}';

    protected $description = 'Imports info.json files downloaded from IIIF';

    public function handle()
    {
        // Only target images whose info.json have been downloaded
        $images = Image::whereNotNull('info_downloaded_at');

        if (!$this->option('all'))
        {
            // Only target images that don't have dimensions yet
            $images = $images->where(function($query) {
                $query->whereNull('width')->orWhereNull('height');
            });
        }

        if (!$this->confirm($images->count() . ' info files will be imported. Proceed?'))
        {
            return;
        }

        foreach ($images->cursor(['id']) as $image)
        {
            $file = "info/{$image->id}.json";

            if (!Storage::exists($file))
            {
                $this->warn("{$image->id} - File not found");
                continue;
            }

            // Parse the JSON file
            $contents = Storage::get($file);
            $contents = json_decode($contents);

            // Save dimensions to database
            $image->width = $contents->width;
            $image->height = $contents->height;

            $image->save();

            $this->info("{$image->id} - saved - {$image->width} x {$image->height}");
        }

        $this->info($images->count() . ' image records processed.');
    }

}
