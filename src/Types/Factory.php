<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

class Factory implements \GeoIO\Factory
{
    public function createPoint($dimension, array $coordinates, $srid = null)
    {
        return new Point($coordinates['y'], $coordinates['x']);
    }

    public function createLineString($dimension, array $points, $srid = null)
    {
        return new LineString($points);
    }

    public function createLinearRing($dimension, array $points, $srid = null)
    {
        return new LineString($points);
    }

    public function createPolygon($dimension, array $lineStrings, $srid = null)
    {
        return new Polygon($lineStrings);
    }

    public function createMultiPoint($dimension, array $points, $srid = null)
    {
        return new MultiPoint($points);
    }

    public function createMultiLineString($dimension, array $lineStrings, $srid = null)
    {
        return new MultiLineString($lineStrings);
    }

    public function createMultiPolygon($dimension, array $polygons, $srid = null)
    {
        return new MultiPolygon($polygons);
    }

    public function createGeometryCollection($dimension, array $geometries, $srid = null)
    {
        return new GeometryCollection($geometries);
    }
}
