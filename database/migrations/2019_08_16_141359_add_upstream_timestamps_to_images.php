<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpstreamTimestampsToImages extends Migration
{

    public function up()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->datetime('dams_modified_at')->nullable()->index()->after('file_modified_at');
            $table->datetime('api_modified_at')->nullable()->index()->after('dams_modified_at');
            $table->datetime('api_imported_at')->nullable()->index()->after('api_modified_at');
        });

    }

    public function down()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn([
                'dams_modified_at',
                'api_modified_at',
                'api_imported_at',
            ]);
        });

    }

}
