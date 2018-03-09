<?php

namespace App\Console\Commands;

use App\Image;

use Illuminate\Support\Facades\Storage;

class ImagesJsonDownload extends AbstractCommand
{

    protected $signature = 'images:json-download';

    protected $description = 'Downloads info.json files from IIIF';

    public function handle()
    {

        ini_set("memory_limit", "-1");

        $images = Image::all('id');

        $images->each( function( $image, $i ) {

            $id = $image->id;

            $file = "info/{$id}.json";
            $url = env('IIIF_URL') . "/{$id}/info.json";

            // Check if file exists
            $exists = Storage::exists( $file );

            if( $exists )
            {
                $this->warn( "Image JSON #{$i}: ID {$id} - already exists" );
                return;
            }

            try {
                $contents = $this->fetch( $url );
                Storage::put( $file, $contents);
                $this->info( "Image JSON #{$i}: ID {$id} - downloaded" );
            }
            catch (\Exception $e) {
                // TODO: Avoid catching non-HTTP exceptions?
                $this->warn( "Image JSON #{$i}: ID {$id} - not found - " . $url );
                return;
            }

        });

    }

}
