<?php

namespace App\Console\Commands\Report;

use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use App\Image;

use App\Console\Commands\AbstractCommand;

class ReportDimensions extends AbstractCommand
{
    protected $signature = 'report:dimensions';

    protected $description = 'Show what dimensions changed between current database and old';

    public function handle()
    {
        ini_set('memory_limit', '-1');

        $csv = Writer::createFromPath(storage_path('app/dimensions.csv'), 'w');

        $csv->insertOne([
            'id',
            'new_width',
            'new_height',
            'old_width',
            'old_height',
        ]);

        foreach (Image::cursor(['id','width','height']) as $image)
        {
            $oldImage = DB::connection('old')->select('select id, width, height from images where id = ?', [$image->id]);

            if (empty($oldImage)) {
                $this->info($image->id . ' - Not found');
                continue;
            }

            $oldImage = $oldImage[0];

            if ($image->width !== $oldImage->width || $image->height !== $oldImage->height) {
                $csv->insertOne([
                    'id' => $image->id,
                    'new_width' => $image->width,
                    'new_height' => $image->height,
                    'old_width' => $oldImage->width,
                    'old_height' => $oldImage->height,
                ]);

                $this->warn($image->id . ' - Changed!');
                continue;
            }

            $this->info($image->id . ' - Skipped');
        }
    }
}
