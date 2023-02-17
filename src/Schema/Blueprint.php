<?php

namespace Grimzy\LaravelMysqlSpatial\Schema;

use Illuminate\Database\Schema\Blueprint as IlluminateBlueprint;

class Blueprint extends IlluminateBlueprint
{
    /**
     * Add a geometry column on the table.
     */
    public function geometry($column, ?int $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('geometry', $column, compact('srid'));
    }

    /**
     * Add a point column on the table.
     *
     * @param  ?int  $srid
     */
    public function point($column, $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('point', $column, compact('srid'));
    }

    /**
     * Add a linestring column on the table.
     */
    public function lineString($column, ?int $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('linestring', $column, compact('srid'));
    }

    /**
     * Add a polygon column on the table.
     */
    public function polygon($column, ?int $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('polygon', $column, compact('srid'));
    }

    /**
     * Add a multipoint column on the table.
     */
    public function multiPoint($column, ?int $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('multipoint', $column, compact('srid'));
    }

    /**
     * Add a multilinestring column on the table.
     */
    public function multiLineString($column, ?int $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('multilinestring', $column, compact('srid'));
    }

    /**
     * Add a multipolygon column on the table.
     */
    public function multiPolygon($column, ?int $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('multipolygon', $column, compact('srid'));
    }

    /**
     * Add a geometrycollection column on the table.
     */
    public function geometryCollection($column, ?int $srid = null): \Illuminate\Support\Fluent
    {
        return $this->addColumn('geometrycollection', $column, compact('srid'));
    }

    /**
     * Specify a spatial index for the table.
     *
     * @param  string|array  $columns
     * @param  string  $name
     */
    public function spatialIndex($columns, $name = null): \Illuminate\Support\Fluent
    {
        return $this->indexCommand('spatial', $columns, $name);
    }

    /**
     * Indicate that the given index should be dropped.
     *
     * @param  string|array  $index
     */
    public function dropSpatialIndex($index): \Illuminate\Support\Fluent
    {
        return $this->dropIndexCommand('dropIndex', 'spatial', $index);
    }
}
