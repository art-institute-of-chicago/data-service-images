<?php

namespace App\Console\Commands;

use App\Image;

use League\Csv\Reader;

class PythonImport extends AbstractCommand
{

    protected $signature = 'python:import';

    protected $description = 'Import CSV for image metadata';

    public function handle()
    {
        $path = storage_path() . '/app/python-output.csv';

        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $row)
        {
            $image = Image::find($row['id']);

            if( !$image ) {
                $this->info("{$row['id']} - not found");
                continue;
            }

            // https://github.com/JohannesBuchner/imagehash
            !empty($row['ahash']) && $image->ahash = $row['ahash'];
            !empty($row['dhash']) && $image->dhash = $row['dhash'];
            !empty($row['phash']) && $image->phash = $row['phash'];
            !empty($row['whash']) && $image->whash = $row['whash'];

            $image->save();

            // Output for reference
            $this->info("{$image->id} - updated");
        }
    }

}
