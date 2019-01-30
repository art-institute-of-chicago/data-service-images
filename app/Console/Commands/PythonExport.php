<?php

namespace App\Console\Commands;

use App\Image;

use League\Csv\Writer;

class PythonExport extends AbstractCommand
{

    protected $signature = 'python:export';

    protected $description = 'Export CSV for image metadata';

    protected $filename = 'images.csv';

    protected $csv;

    public function handle()
    {

        ini_set("memory_limit", "-1");

        $path = storage_path() . '/app/' . $this->filename;

        $this->csv = Writer::createFromPath( $path, 'w' );
        $this->csv->insertOne( ['id', 'h', 's', 'l', 'population', 'percentage'] );

        $images = Image::all();

        $this->info( $images->count() . ' images found.' );

        // Uncomment for testing
        // $images = $images->slice( 0, 5 );

        $images->map( [$this, 'getRow'] );

    }

    public function getRow( $image )
    {

        $row = [
            'id' => $image->getKey(),
            'h' => $image->color->h ?? null,
            's' => $image->color->s ?? null,
            'l' => $image->color->l ?? null,
            'population' => $image->color->population ?? null,
            'percentage' => $image->color->percentage ?? null,
        ];

        $this->csv->insertOne( $row );

        $this->info( json_encode( $row ) );

    }

}
