<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geometry', function (Blueprint $table) {
            $table->increments('id');
            $table->geometry('geo')->default(null)->nullable();
            $table->point('location')->default(null)->nullable();
            $table->linestring('line')->default(null)->nullable();
            $table->polygon('shape')->default(null)->nullable();
            $table->multipoint('multi_locations')->default(null)->nullable();
            $table->multilinestring('multi_lines')->default(null)->nullable();
            $table->multipolygon('multi_shapes')->default(null)->nullable();
            $table->geometrycollection('multi_geometries')->default(null)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('geometry');
    }
}