<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->point('location');  // required to be not null in order to add an index
            $table->lineString('line')->default(null)->nullable();
            $table->polygon('shape')->default(null)->nullable();
            $table->multiPoint('multi_locations')->default(null)->nullable();
            $table->multiLineString('multi_lines')->default(null)->nullable();
            $table->multiPolygon('multi_shapes')->default(null)->nullable();
            $table->geometryCollection('multi_geometries')->default(null)->nullable();
            $table->timestamps();
        });

        Schema::create('no_spatial_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->geometry('geometry')->default(null)->nullable();
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
        Schema::drop('no_spatial_fields');
    }
}
