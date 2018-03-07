<?php

namespace App\Console\Commands;

use Aic\Hub\Foundation\AbstractCommand as BaseCommand;

abstract class AbstractCommand extends BaseCommand
{

    private function fetch( $file, $decode = false ) {

        if( !$contents = @file_get_contents( $file ) )
        {
            throw new \Exception('Load Failed');
        }

        return $decode ? json_decode( $contents ) : $contents;

    }

}


