<?php

namespace App\Console\Commands;

use Aic\Hub\Foundation\AbstractCommand as BaseCommand;

abstract class AbstractCommand extends BaseCommand
{

    protected function fetch($file, &$headers = null)
    {
        if(!$contents = @file_get_contents($file))
        {
            throw new \Exception('Fetch failed: ' . $file);
        }

        if (isset($http_response_header))
        {
            $headers = $http_response_header;
        }

        return $contents;
    }

}


