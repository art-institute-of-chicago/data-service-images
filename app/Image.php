<?php

namespace App;

use Aic\Hub\Foundation\AbstractModel as BaseModel;

class Image extends BaseModel
{

    protected $casts = [
        'color' => 'object',
    ];

}
