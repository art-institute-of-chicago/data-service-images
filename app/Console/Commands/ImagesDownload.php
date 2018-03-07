<?php

namespace App\Console\Commands;

use App\Image;

use Illuminate\Support\Facades\Storage;

class ImagesDownload extends AbstractCommand
{

    protected $signature = 'images:download
                            {--skip=? : Manual offset for downloading}
                            {--forget : Do not track offset for later}';

    protected $description = 'Downloads all images from LAKE IIIF';

    public function handle()
    {

        ini_set("memory_limit", "-1");

        $lastSkipFile = 'lastImageSkip.txt';

        // Determine our $skip from last time
        $skip = Storage::exists( $lastSkipFile ) ? (int) Storage::get( $lastSkipFile ) : 0;

        if( $this->option('skip') )
        {
            $skip = (int) $this->option('skip');
        }

        $count = Image::count();
        $take = 10;

        while( $skip < $count )
        {

            // TODO: Avoid hardcoding the `id` field. Use singleton and getKeyName().
            // https://stackoverflow.com/questions/35643192/laravel-eloquent-limit-and-offset
            $ids = Image::skip( $skip )->take( $take )->get(['id'])->pluck('id');

            $ids->each( function( $id, $i ) use ( $skip ) {

                $n = $i + $skip;
                $file = "images/{$id}.jpg";
                $url = env('IIIF_URL') . "/{$id}/full/!800,800/0/default.jpg";

                // Check if file exists
                $exists = Storage::exists( $file );

                if( $exists )
                {
                    $this->warn( "Image #{$n}: ID {$id} - already exists" );
                    return;
                }

                try {
                    $contents = $this->fetch( $url );
                    Storage::put( $file, $contents);
                    $this->info( "Image #{$n}: ID {$id} - downloaded" );
                }
                catch (\Exception $e) {
                    $this->warn( "Image #{$n}: ID {$id} - not found" );
                    return;
                }


            });

            // Advance our counter
            $skip += $take;

            if( !$this->option('forget') )
            {
               Storage::put( $lastSkipFile, $skip );
            }

        }

    }

}
