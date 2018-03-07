<?php

namespace App\Console\Commands;

use App\Image;

use League\Csv\Reader;

class ImagesCsvImport extends AbstractCommand
{

    protected $signature = 'images:csv-import';

    protected $description = 'Import CSV for image metadata';

    protected $filename = 'images.csv';

    public function handle()
    {

        ini_set("memory_limit", "-1");

        $path = storage_path() . '/app/' . $this->filename;

        $csv = Reader::createFromPath( $path, 'r' );
        $csv->setHeaderOffset(0);

        foreach( $csv->getRecords() as $row )
        {

            // Save to Image metadata
            $image = Image::find( $row['id'] );

            // Image not found
            if( !$image ) {
                continue;
            }

            // TODO: Potential items from the Python implementation:
            // $image->fingerprint = $this->getFingerprint( $image, $row );
            // $image->mse = $row['mse'] ?? null;

            // For now, just export the dominant color:
            $image->color = $this->getColor( $image, $row );

            $image->save();

            // Output for reference
            $this->info( $image->getKey() . ' = ' . json_encode( $image->color ) );

        }

    }

    private function getColor( $image, $row )
    {

        $color = $image->color ?? (object) [];

        $color->h = (int) $row['h'] ?? null;
        $color->s = (int) $row['s'] ?? null;
        $color->l = (int) $row['l'] ?? null;
        $color->population = (int) $row['population'] ?? null;
        $color->percentage = (float) $row['percentage'] ?? null;

        return $color;

    }

    private function getFingerprint( $image, $row )
    {

        $fingerprint = $image->fingerprint ?? (object) [];

        $fingerprint->ahash = $row['ahash'] ?? null;
        $fingerprint->dhash = $row['dhash'] ?? null;
        $fingerprint->phash = $row['phash'] ?? null;
        $fingerprint->whash = $row['whash'] ?? null;

        return $fingerprint;

    }

}
