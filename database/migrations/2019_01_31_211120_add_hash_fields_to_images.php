<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHashFieldsToImages extends Migration
{

    public function up()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->string('ahash', 16)->nullable()->after('color');
            $table->string('phash', 16)->nullable()->after('ahash');
            $table->string('dhash', 16)->nullable()->after('phash');
            $table->string('whash', 16)->nullable()->after('dhash');
        });

    }

    public function down()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('ahash');
            $table->dropColumn('phash');
            $table->dropColumn('dhash');
            $table->dropColumn('whash');
        });

    }

}
