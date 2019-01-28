<?php

namespace App\Providers;

use App\Library\Database\MySqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Override the default connection for MySQL. This allows us to use `replace` etc.
     *
     * @link https://stidges.com/extending-the-connection-class-in-laravel
     * @link https://gist.github.com/VinceG/0fb570925748ab35bc53f2a798cb517c
     *
     * @return void
     */
    public function boot()
    {
        Connection::resolverFor('mysql', function($connection, $database, $prefix, $config) {
            return new MySqlConnection($connection, $database, $prefix, $config);
        });
    }
}
