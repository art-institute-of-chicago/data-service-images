<?php

namespace App\Jobs;

use App\Image;

use Illuminate\Support\Facades\Storage;

class JsonDownload extends AbstractJob
{

    protected $id;
    protected $i;

    public function __construct(Image $image, $i)
    {

        $this->id = $image->id;
        $this->i = $i;

    }

    public function handle()
    {

        $id = $this->id;
        $i = $this->i;

        $file = "info/{$id}.json";
        $url = env('IIIF_URL') . "/{$id}/info.json";

        // Check if file exists
        $exists = Storage::exists( $file );

        if( $exists )
        {
            // Commenting this out so we can re-download for testing
            // Still figuring out how to log job results
            // return "Image JSON #{$i}: ID {$id} - already exists";
        }

        try {
            $contents = $this->fetch( $url );
            Storage::put( $file, $contents);
            return 0;
        }
        catch (\Exception $e) {
            // TODO: Avoid catching non-HTTP exceptions?
            return "Image JSON #{$i}: ID {$id} - not found - " . $url;
        }

    }

}
