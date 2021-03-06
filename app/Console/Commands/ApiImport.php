<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiImport extends AbstractCommand
{

    protected $signature = 'api:import';

    protected $description = 'Imports core image data from the data-aggregator';

    protected $chunkSize = 500;

    protected $urlFormat;

    public function handle()
    {
        // Prep URL $format for sprintf calls
        $this->urlFormat = env('API_URL') . '/images?' . urldecode(http_build_query([
            'page' => '%d',
            'limit' => '%d',
            'fields' => implode(',', [
                'id',
                'title',
                'content_e_tag',
                'content_modified_at',
                'last_updated_source',
                'last_updated',
            ]),
        ]));

        // Query for the first page + get total
        $json = $this->query(1, 0);

        // Assumes the dataservice has standardized pagination
        $total = $json->pagination->total;
        $totalPages = ceil($total/$this->chunkSize);

        $bar = $this->output->createProgressBar($total);

        for ($currentPage = 1; $currentPage <= $totalPages; $currentPage++)
        {
            $json = $this->query($currentPage, $this->chunkSize);

            $now = date('Y-m-d H:i:s');

            // TODO: Implement actual inbound transformer?
            $data = array_map(function($datum) use ($now) {
                // Manually append timestamps
                $datum->created_at = $now;
                $datum->updated_at = $now;

                // Rename `content` fields to `file`
                $datum->file_e_tag = $datum->content_e_tag ?? null;
                $datum->file_modified_at = $this->getDate($datum->content_modified_at ?? null);

                unset($datum->content_e_tag);
                unset($datum->content_modified_at);

                // Set api-related timestamps
                $datum->dams_modified_at = $this->getDate($datum->last_updated_source ?? null);
                $datum->api_modified_at = $this->getDate($datum->last_updated ?? null);
                $datum->api_imported_at = $now;

                unset($datum->last_updated_source);
                unset($datum->last_updated);

                // Encode any stdClass to strings
                return array_map(function($value) {
                    return is_object($value) ? json_encode($value) : $value;
                }, (array) $datum);
            }, $json->data);

            // https://gist.github.com/VinceG/0fb570925748ab35bc53f2a798cb517c
            // insertUpdate needs more work to be suitable for batch use
            DB::table('images')->insertUpdate($data);

            $bar->advance(count($data));
        }

        $bar->finish();
        $this->output->newLine(1);
    }

    protected function query($page, $limit)
    {
        return json_decode($this->fetch(sprintf($this->urlFormat, $page, $limit)));
    }

    protected function getDate($value)
    {
        if (!isset($value)) {
            return null;
        }

        $date = Carbon::parse($value);
        $date->setTimezone('UTC');

        return $date->format('Y-m-d H:i:s');
    }
}
