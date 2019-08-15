<?php

namespace App;

use Aic\Hub\Foundation\AbstractModel as BaseModel;

class Image extends BaseModel
{

    protected $casts = [
        'color' => 'object',
        'file_modified_at' => 'datetime',
        'info_attempted_at' => 'date',
        'info_downloaded_at' => 'date',
        'image_attempted_at' => 'date',
        'image_downloaded_at' => 'date',
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
