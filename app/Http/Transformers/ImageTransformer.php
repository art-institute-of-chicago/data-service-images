<?php

namespace App\Http\Transformers;

use Aic\Hub\Foundation\AbstractTransformer;

class ImageTransformer extends AbstractTransformer
{

    public function transform($image)
    {

        $data = [
            'id' => $image->id,
            'title' => $image->title,

            // From info.json
            'width' => $image->width,
            'height' => $image->height,

            // From Python subservice
            'ahash' => $image->ahash,
            'phash' => $image->phash,
            'dhash' => $image->dhash,
            'whash' => $image->whash,
            'colorfulness' => $image->colorfulness,

            // From artisan commands
            'lqip' => $image->lqip,
            'color' => $image->color,
        ];

        // Enables ?fields= functionality
        return parent::transform($data);

    }

}
