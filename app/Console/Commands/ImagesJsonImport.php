<?php

namespace App\Console\Commands;

use App\Image;

use Illuminate\Support\Facades\Storage;

class ImagesJsonImport extends AbstractCommand
{

    protected $signature = 'images:json-import';

    protected $description = 'Downloads info.json files from IIIF';

    public function handle()
    {

        ini_set("memory_limit", "-1");

        // Use this to grab ids for all images
        // $images = Image::all('id');

        // Use this to only target images that don't have dimensions yet
        $images = Image::whereNull('width')->orWhereNull('height')->get(['id']);

        $images->each( function( $image, $i ) {

            $id = $image->id;

            $file = "info/{$id}.json";
            $url = env('IIIF_URL') . "/{$id}/info.json";

            // Check if file exists
            $exists = Storage::exists( $file );

            if( !$exists )
            {
                $this->warn( "Image JSON #{$i}: ID {$id} - not found - " . $file );
                return;
            }

            // Parse the JSON file
            $contents = Storage::get( $file );
            $contents = json_decode( $contents );

            // Save dimensions to database
            $image->width = $contents->width;
            $image->height = $contents->height;

            $image->save();

            $this->info( "Image JSON #{$i}: ID {$id} - saved - {$image->width} x {$image->height}" );

        });

    }

}
