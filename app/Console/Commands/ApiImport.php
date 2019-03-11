<?php

namespace App\Console\Commands;

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
                'width',
                'height',
                'lqip',
                'color',
                'content_e_tag',
                'content_modified_at',
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

            // Encode any stdClass to strings
            $data = array_map(function($datum) {

                // TODO: Implement actual inbound transformer?
                $datum->file_e_tag = $datum->content_e_tag ?? null;
                $datum->file_modified_at = $this->getDate($datum->content_modified_at ?? null);

                unset($datum->content_e_tag);
                unset($datum->content_modified_at);

                return array_map(function($value) {
                    return is_object($value) ? json_encode($value) : $value;
                }, (array) $datum);
            }, $json->data);

            // https://gist.github.com/VinceG/0fb570925748ab35bc53f2a798cb517c
            // insertUpdate needs more work to be suitable for batch use
            DB::table('images')->insertIgnore($data);

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
        return isset($value) ? date('Y-m-d H:i:s', strtotime($value)) : null;
    }
}
