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

            // Upstream metadata
            'dams_modified_at' => $this->getDateValue($image, 'dams_modified_at'),
            'api_modified_at' => $this->getDateValue($image, 'api_modified_at'),
            'api_imported_at' => $this->getDateValue($image, 'api_imported_at'),

            // Import metadata
            'info_attempted_at' => $this->getDateValue($image, 'info_attempted_at'),
            'info_downloaded_at' => $this->getDateValue($image, 'info_downloaded_at'),
            'image_attempted_at' => $this->getDateValue($image, 'image_attempted_at'),
            'image_downloaded_at' => $this->getDateValue($image, 'image_downloaded_at'),

            // Cache hit metadata
            'image_cache_hit' => $image->image_cache_hit,
            'info_cache_hit' => $image->info_cache_hit,

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
