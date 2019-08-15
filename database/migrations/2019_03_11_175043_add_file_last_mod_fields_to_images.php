<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileLastModFieldsToImages extends Migration
{

    public function up()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->string('file_e_tag', 40)->nullable()->after('whash');
            $table->datetime('file_modified_at')->nullable()->index()->after('file_e_tag');
        });

    }

    public function down()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn([
                'file_e_tag',
                'file_modified_at',
            ]);
        });

    }

}
