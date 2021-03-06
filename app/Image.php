<?php

namespace App;

use Aic\Hub\Foundation\AbstractModel as BaseModel;

class Image extends BaseModel
{

    protected $casts = [
        'color' => 'object',
        'file_modified_at' => 'datetime',
        'dams_modified_at' => 'datetime',
        'api_modified_at' => 'datetime',
        'api_imported_at' => 'datetime',
        'image_attempted_at' => 'datetime',
        'image_downloaded_at' => 'datetime',
        'image_cache_hit' => 'boolean',
    ];

    /**
     * Ensure that the id is a valid UUID.
     *
     * @param mixed $id
     * @return boolean
     */
    public static function validateId($id)
    {

        // We must not be using UUIDv3, since the typical regex wasn't matching
        $uuid = '/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i';

        return preg_match($uuid, $id);

    }

}
