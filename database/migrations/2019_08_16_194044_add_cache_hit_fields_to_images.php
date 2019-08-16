<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCacheHitFieldsToImages extends Migration
{

    public function up()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->boolean('image_cache_hit')->nullable()->after('info_downloaded_at');
            $table->boolean('info_cache_hit')->nullable()->after('image_cache_hit');
        });

    }

    public function down()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn([
                'image_cache_hit',
                'info_cache_hit',
            ]);
        });

    }

}
