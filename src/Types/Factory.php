<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

class Factory implements \GeoIO\Factory
{
    public function createPoint($dimension, array $coordinates, $srid = null)
    {
        return new Point($coordinates['y'], $coordinates['x'], $srid);
    }

    public function createLineString($dimension, array $points, $srid = null)
    {
        return new LineString($points, $srid);
    }

    public function createLinearRing($dimension, array $points, $srid = null)
    {
        return new LineString($points, $srid);
    }

    public function createPolygon($dimension, array $lineStrings, $srid = null)
    {
        return new Polygon($lineStrings, $srid);
    }

    public function createMultiPoint($dimension, array $points, $srid = null)
    {
        return new MultiPoint($points, $srid);
    }

    public function createMultiLineString($dimension, array $lineStrings, $srid = null)
    {
        return new MultiLineString($lineStrings, $srid);
    }

    public function createMultiPolygon($dimension, array $polygons, $srid = null)
    {
        return new MultiPolygon($polygons, $srid);
    }

    public function createGeometryCollection($dimension, array $geometries, $srid = null)
    {
        return new GeometryCollection($geometries, $srid);
    }
}
