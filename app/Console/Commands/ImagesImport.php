<?php

namespace App\Console\Commands;

use App\Image;

class ImagesImport extends AbstractCommand
{

    protected $signature = 'images:import';

    protected $description = 'Imports core image data from the data-aggregator';

    public function handle()
    {

        ini_set("memory_limit", "-1");

        $this->import( Image::class, 'images' );

    }

    protected function save( $datum, $model )
    {

        // TODO: Make inbound transformer report the id key sourceside?
        $image = $model::findOrNew( $datum->id );

        $image->id = $datum->id;
        $image->title = $datum->title;

        $image->save();

    }

}
