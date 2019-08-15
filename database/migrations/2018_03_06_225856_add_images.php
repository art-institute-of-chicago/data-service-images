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

            $table->datetime('created_at')->nullable()->index();
            $table->datetime('updated_at')->nullable()->index();
        });

    }

    public function down()
    {

        Schema::dropIfExists('images');

    }

}
