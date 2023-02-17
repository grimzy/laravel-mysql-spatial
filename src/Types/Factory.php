<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

class Factory implements \GeoIO\Factory
{
    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createPoint($dimension, array $coordinates, $srid = 0): Point
    {
        return new Point($coordinates['y'], $coordinates['x'], $srid);
    }

    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createLineString($dimension, array $points, $srid = 0): LineString
    {
        return new LineString($points, $srid);
    }

    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createLinearRing($dimension, array $points, $srid = 0): LineString
    {
        return new LineString($points, $srid);
    }

    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createPolygon($dimension, array $lineStrings, $srid = 0): Polygon
    {
        return new Polygon($lineStrings, $srid);
    }

    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createMultiPoint($dimension, array $points, $srid = 0): MultiPoint
    {
        return new MultiPoint($points, $srid);
    }

    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createMultiLineString($dimension, array $lineStrings, $srid = 0): MultiLineString
    {
        return new MultiLineString($lineStrings, $srid);
    }

    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createMultiPolygon($dimension, array $polygons, $srid = 0): MultiPolygon
    {
        return new MultiPolygon($polygons, $srid);
    }

    /**
     * @param  mixed  $dimension
     * @param  ?int  $srid
     */
    public function createGeometryCollection($dimension, array $geometries, $srid = 0): GeometryCollection
    {
        return new GeometryCollection($geometries, $srid);
    }
}
