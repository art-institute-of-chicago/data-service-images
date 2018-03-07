<?php

namespace App\Console\Commands;

use Aic\Hub\Foundation\AbstractCommand as BaseCommand;

abstract class AbstractCommand extends BaseCommand
{

    private function fetch( $file, $decode = false ) {

        if( !$contents = @file_get_contents( $file ) )
        {
            throw new \Exception('Fetch failed: ' . $file );
        }

        return $decode ? json_decode( $contents ) : $contents;

    }

    protected function query( $endpoint, $page = 1, $limit = 1000 )
    {

        // TODO: Make API_URL more generic
        // TODO: Allow passing `fields` param
        $url = env('API_URL') . '/' . $endpoint . '?page=' . $page . '&limit=' . $limit . '&fields=id,title';

        $this->info( 'Querying: ' . $url );

        return $this->fetch( $url, true );

    }

    protected function import( $model, $endpoint, $current = 1 )
    {

        // Query for the first page + get page count
        $json = $this->query( $endpoint, $current );

        // Assumes the dataservice has standardized pagination
        $pages = $json->pagination->total_pages;

        while( $current <= $pages )
        {

            foreach( $json->data as $datum )
            {

                $this->save( $datum, $model );

            }

            $current++;

            $json = $this->query( $endpoint, $current );

        }

    }

    protected function save( $datum, $model )
    {

        return null;

    }

}


