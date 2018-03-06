<?php

namespace App\Http\Controllers;

use Aic\Hub\Foundation\AbstractController as BaseController;

class ImageController extends BaseController
{

    protected $model = \App\Image::class;

    protected $transformer = \App\Http\Transformers\ImageTransformer::class;

}
