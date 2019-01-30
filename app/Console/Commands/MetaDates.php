<?php

namespace App\Console\Commands;

use App\Image;

use Illuminate\Support\Facades\Storage;

class MetaDates extends AbstractCommand
{

    protected $signature = 'meta:dates';

    protected $description = 'Fills out date fields based on file metadata';

    public function handle()
    {
        $this->setInfoFields();
        $this->setImageFields();
    }

    private function setInfoFields()
    {
        // Only target images whose info.json have not been downloaded
        $images = Image::whereNull('info_attempted_at')->orWhereNull('info_downloaded_at');

        foreach ($images->cursor(['id']) as $image)
        {
            $file = storage_path() . "/app/info/{$image->id}.json";

            if (!file_exists($file))
            {
                $this->warn("{$image->id} - Info file not found");
                continue;
            }

            // Get file modified time
            $mtime = filemtime($file);

            $image->info_downloaded_at = $mtime;

            if (!isset($image->info_attempted_at))
            {
                $image->info_attempted_at = $mtime;
            }

            $image->save();

            $this->info("{$image->id} - Info dates updated");
        }

        $this->info($images->count() . ' image records processed.');
    }

    private function setImageFields()
    {
        // Only target images whose image files have not been downloaded
        $images = Image::whereNull('image_attempted_at')->orWhereNull('image_downloaded_at');

        foreach ($images->cursor(['id']) as $image)
        {
            $file = storage_path() . "/app/images/{$image->id}.jpg";

            if (!file_exists($file))
            {
                $this->warn("{$image->id} - Image file not found");
                continue;
            }

            // Get file modified time
            $mtime = filemtime($file);

            $image->image_downloaded_at = $mtime;

            if (!isset($image->image_attempted_at))
            {
                $image->image_attempted_at = $mtime;
            }

            $image->save();

            $this->info("{$image->id} - Image dates updated");
        }

        $this->info($images->count() . ' image records processed.');
    }

}
