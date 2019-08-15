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

            // File metadata
            'file_e_tag' => $image->file_e_tag,
            'file_modified_at' => $this->getDateValue($image, 'file_modified_at'),

            // Record metadata
            'created_at' => $this->getDateValue($image, 'updated_at'),
            'modified_at' => $this->getDateValue($image, 'created_at'),
        ];

        // Enables ?fields= functionality
        return parent::transform($data);

    }

    private function getDateValue($image, $fieldName)
    {
        if (!isset($image->{$fieldName})) {
            return null;
        }

        $date = $image->{$fieldName};
        $date->setTimezone('America/Chicago');

        return $date->toIso8601String();
    }

}
