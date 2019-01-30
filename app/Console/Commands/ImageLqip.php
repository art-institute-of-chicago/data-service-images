<?php

namespace App\Console\Commands;

use App\Image;

class ImageLqip extends AbstractCommand
{

    protected $signature = 'image:lqip';

    protected $description = 'Generates low quality image placeholders (LQIPs)';

    public function handle()
    {
        // For now, only target images that don't have an LQIP
        $images = Image::whereNull('lqip');

        foreach ($images->cursor() as $image)
        {
            // Skip the image if it has an lqip
            if ($image->lqip)
            {
                $this->warn($image->id . ' - ' . 'Already has LQIP');
                continue;
            }

            // Get the file using the id
            $source = storage_path() . "/app/images/{$image->id}.jpg";

            // Skip the image if its file doesn't exist
            if (!file_exists($source))
            {
                $this->warn($image->id . ' - ' . 'File not found');
                continue;
            }

            // Generate an Imagemagick command
            $cmd = sprintf('convert "%s" -resize x5 inline:gif:-', $source);

            // Run the command and grab its output
            $lqip = exec($cmd);

            // Skip if the $lquip is blank
            if (empty($lqip))
            {
                $this->warn($image->id . ' - ' . 'Cannot create LQIP');
                continue;
            }

            // Remove data:image/gif;base64,
            // $lqip = substr( $lqip, 22 );

            // Remove R0lGODlh (GIF magic number)
            // $lqip = substr( $lqip, 8 );

            // Save the LQIP to database
            $image->lqip = $lqip;
            $image->save();

            $this->info($image->id . ' - ' . 'Added LQIP');
        }

        $this->info($images->count() . ' image records processed.');
    }

}
