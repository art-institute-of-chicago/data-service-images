<?php

namespace App\Console\Commands;

use App\Image;

use League\Csv\Writer;

class PythonExport extends AbstractCommand
{

    protected $signature = 'python:export';

    protected $description = 'Export CSV of image ids for Python processing';

    public function handle()
    {
        $path = storage_path() . '/app/python-input.csv';

        $csv = Writer::createFromPath($path, 'w');

        $csv->insertOne([
            'id',
            // 'colorfullness',
        ]);

        $images = Image::query();
            // ->whereNull('colorfullness');

        foreach ($images->cursor() as $image)
        {
            $row = [
                'id' => $image->id,
                // 'colorfullness' => isset($image->colorfullness) ? null : true,
            ];

            $csv->insertOne($row);

            $this->info(json_encode($row));
        }
    }

}
