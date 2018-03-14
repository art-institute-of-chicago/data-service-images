<?php

namespace App\Console\Commands;

use App\Image;
use App\Jobs\JsonDownload;

class ImagesJsonDownload extends AbstractCommand
{

    protected $signature = 'images:json-download';

    protected $description = 'Downloads info.json files from IIIF';

    public function handle()
    {

        ini_set("memory_limit", "-1");

        // Uncomment for real work
        // $images = Image::all('id');

        // Added for testing
        $images = Image::take(5)->get(['id']);

        $images->each( function( $image, $i ) {

            JsonDownload::dispatch( $image, $i );

        });

    }

}
