<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Integration\Migrations;

use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class UpdateTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL < 5.7.5: table has to be MyISAM
        \DB::statement('ALTER TABLE geometry ENGINE = MyISAM');

        Schema::table('geometry', function (Blueprint $table) {
            // Make sure point is not nullable
            $table->point('location')->change();

            // The other field changes are just here to test if change works with them, we're not changing anything
            $table->geometry('geo')->default(null)->nullable()->change();
            $table->lineString('line')->default(null)->nullable()->change();
            $table->polygon('shape')->default(null)->nullable()->change();
            $table->multiPoint('multi_locations')->default(null)->nullable()->change();
            $table->multiLineString('multi_lines')->default(null)->nullable()->change();
            $table->multiPolygon('multi_shapes')->default(null)->nullable()->change();
            $table->geometryCollection('multi_geometries')->default(null)->nullable()->change();

            // Add a spatial index on the location field
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('geometry', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']); // either an array of column names or the index name
        });

        \DB::statement('ALTER TABLE geometry ENGINE = InnoDB');

        Schema::table('geometry', function (Blueprint $table) {
            $table->point('location')->nullable()->change();
        });
    }
}
