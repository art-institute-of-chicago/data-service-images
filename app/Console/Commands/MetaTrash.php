<?php

namespace App\Console\Commands;

use DirectoryIterator;

use App\Image;

class MetaTrash extends AbstractCommand
{

    protected $signature = 'meta:trash';

    protected $description = 'Trash info and image files without database records';

    public function handle()
    {
        $this->trashInfoFiles();
        $this->trashImageFiles();
    }

    private function trashInfoFiles()
    {
        $files = new DirectoryIterator(storage_path() . '/app/info');

        foreach ($files as $file)
        {
            $id = substr(basename($file), 0, -5);

            if (!Image::find($id))
            {
                $this->info('Trashed ' . $file);
                rename($file->getPathname(), storage_path() . '/app/info-trash/' . $id . '.json');
            }
        }
    }

    private function trashImageFiles()
    {
        $files = new DirectoryIterator(storage_path() . '/app/images');

        foreach ($files as $file)
        {
            $id = substr(basename($file), 0, -4);

            if (!Image::find($id))
            {
                $this->info('Trashed ' . $file);
                rename($file->getPathname(), storage_path() . '/app/images-trash/' . $id . '.jpg');
            }
        }
    }

}
