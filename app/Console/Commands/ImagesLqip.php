<?php

namespace App\Console\Commands;

use App\Image;

class ImagesLqip extends AbstractCommand
{

    protected $signature = 'images:lqip';

    protected $description = 'Generates low quality image placeholders (LQIPs)';

    public function handle()
    {

        ini_set("memory_limit", "-1");

        $images = Image::all();

        $images->each( function( $image, $i ) {

            // For now, skip the image if it has an lqip
            if( $image->lqip )
            {
                $this->warn( $i . ' - ' . $image->id . ' - ' . 'Already has LQIP' );
                return null;
            }

            // Get the file using the id
            $source =  storage_path() . '/app/' . "images/{$image->id}.jpg";

            // Generate an Imagemagick command
            $cmd = sprintf( 'convert "%s" -resize x5 inline:gif:-', $source );

            // Run the command and grab its output
            $lqip = exec( $cmd );

            // Remove data:image/gif;base64,
            // $lqip = substr( $lqip, 22 );

            // Remove R0lGODlh (GIF magic number)
            // $lqip = substr( $lqip, 8 );

            // Save the LQIP to database
            $image->lqip = $lqip;
            $image->save();

            $this->info( $i . ' - ' . $image->id . ' - ' . 'Added LQIP' );

        });

    }

}
