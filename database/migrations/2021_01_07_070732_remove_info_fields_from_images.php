<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveInfoFieldsFromImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn([
                'info_attempted_at',
                'info_downloaded_at',
                'info_cache_hit',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->datetime('info_attempted_at')->nullable()->index()->after('image_downloaded_at');
            $table->datetime('info_downloaded_at')->nullable()->index()->after('info_attempted_at');
            $table->boolean('info_cache_hit')->nullable()->after('image_cache_hit');
        });
    }
}
