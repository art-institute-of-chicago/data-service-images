<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDownloadedAtToImages extends Migration
{

    public function up()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->datetime('image_attempted_at')->nullable()->index()->after('color');
            $table->datetime('image_downloaded_at')->nullable()->index()->after('image_attempted_at');
            $table->datetime('info_attempted_at')->nullable()->index()->after('image_downloaded_at');
            $table->datetime('info_downloaded_at')->nullable()->index()->after('info_attempted_at');
        });

    }

    public function down()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('image_attempted_at');
            $table->dropColumn('image_downloaded_at');
            $table->dropColumn('info_attempted_at');
            $table->dropColumn('info_downloaded_at');
        });

    }

}
