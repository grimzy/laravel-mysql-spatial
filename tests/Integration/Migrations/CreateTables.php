<?php

use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
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
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
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

        Schema::create('with_srid', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->increments('id');
            $table->geometry('geo', 3857)->default(null)->nullable();
            $table->point('location', 3857)->default(null)->nullable();
            $table->lineString('line', 3857)->default(null)->nullable();
            $table->polygon('shape', 3857)->default(null)->nullable();
            $table->multiPoint('multi_locations', 3857)->default(null)->nullable();
            $table->multiLineString('multi_lines', 3857)->default(null)->nullable();
            $table->multiPolygon('multi_shapes', 3857)->default(null)->nullable();
            $table->geometryCollection('multi_geometries', 3857)->default(null)->nullable();
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
        Schema::drop('with_srid');
    }
}
