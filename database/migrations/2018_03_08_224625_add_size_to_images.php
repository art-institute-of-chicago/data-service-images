<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSizeToImages extends Migration
{

    public function up()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->integer('width')->nullable()->after('title');
            $table->integer('height')->nullable()->after('width');
        });

    }


    public function down()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('width');
            $table->dropColumn('height');
        });

    }

}
