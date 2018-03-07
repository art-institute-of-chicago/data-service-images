<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColorToImages extends Migration
{

    public function up()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->json('color')->nullable()->after('title');
        });

    }


    public function down()
    {

        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('color');
        });

    }

}
