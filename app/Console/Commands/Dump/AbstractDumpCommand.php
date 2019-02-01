<?php

namespace App\Console\Commands\Dump;

use Illuminate\Support\Facades\Storage;
use Exception;

use Aic\Hub\Foundation\AbstractCommand as BaseCommand;

abstract class AbstractDumpCommand extends BaseCommand
{

    /**
     * In the interest of defensive coding, you must explicitly whitelist
     * which tables should be exported and imported. Use `dump:audit` to
     * see a list of which tables will be excluded.
     */
    protected $whitelistedTables = [
        'images',
    ];

    /**
     * All of the data dumps live in `database/dumps` per `config/filesystems.php`.
     * Use this to generate absolute paths to CSV files for `createFromPath` calls.
     *
     * @param string $subpath  ...e.g. to CSV file, relative to `database/dumps`
     * @return string
     */
    protected function getDumpPath(string $subpath) : string
    {

        return Storage::disk('dumps')->getDriver()->getAdapter()->getPathPrefix() . $subpath;

    }

    /**
     * If command has `--path=` option, return it. Fall back to `database/dumps/local`.
     * Enforces correct structure in dump directory.
     *
     * @return string
     */
    protected function getDumpPathOption() : string
    {
        $dumpPath = $this->hasOption('path') ? $this->option('path') : null;
        $dumpPath = $dumpPath ?? $this->getDumpPath('local');
        $dumpPath = rtrim($dumpPath, '/') . '/';

        if (!file_exists($dumpPath))
        {
            throw new Exception('Directory does not exist: ' . $dumpPath);
        }

        foreach (['tables'] as $subdir)
        {
            $subdirPath = $dumpPath . '/' . $subdir;

            if (!file_exists($subdirPath))
            {
                mkdir($subdirPath, 0755);
            }
        }

        return $dumpPath;
    }

    /**
     * Use this when you need to run a command interactively or show ouput.
     */
    protected function passthru(string $template, string ...$args)
    {
        return $this->command($template, $args, function(string $cmd) {
            passthru($cmd, $status);
            return [
                'output' => null,
                'status' => $status,
            ];
        });
    }

    /**
     * Use this when you need to capture command output in a variable.
     */
    protected function exec(string $template, string ...$args) : array
    {
        return $this->command($template, $args, function(string $cmd) {
            exec($cmd, $output, $status);
            return [
                'output' => $output,
                'status' => $status,
            ];
        });
    }

    /**
     * Sanitize and run shell command. Exit if it fails. Return output or null.
     */
    protected function command(string $template, array $args, callable $callback)
    {
        $args = array_map('escapeshellarg', $args);
        $cmd = vsprintf($template, $args);

        $this->warn($cmd);

        $return = $callback($cmd);

        $this->warn('Status: ' . $return['status']);

        if ($return['status'] !== 0)
        {
            $this->warn('Something went wrong. Exiting early.');
            exit(1);
        }

        return $return['output'];
    }

    /**
     * Throw an exception if an `.env` var is empty.
     */
    protected function validateEnv(array $vars)
    {
        foreach ($vars as $var)
        {
            if (empty(env($var)))
            {
                throw new Exception('Please specify `' . $var . '` in .env');
            }
        }
    }

}
