<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImages extends Migration
{

    public function up()
    {

        Schema::create('images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('title')->nullable();
            $table->timestamps();
        });

    }

    public function down()
    {

        Schema::dropIfExists('images');

    }

}
