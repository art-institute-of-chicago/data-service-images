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
            'width' => $image->width,
            'height' => $image->height,
            'lqip' => $image->lqip,
            'color' => $image->color,
        ];

        // Enables ?fields= functionality
        return parent::transform($data);

    }

}
