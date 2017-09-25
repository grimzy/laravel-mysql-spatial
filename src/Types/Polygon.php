<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

class Polygon extends MultiLineString
{
    public function toWKT()
    {
        return sprintf('POLYGON(%s)', (string) $this);
    }

    /**
     * Convert to GeoJson Polygon that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\Polygon
     */
    public function jsonSerialize()
    {
        $linearrings = [];
        foreach ($this->items as $linestring) {
            $linearrings[] = new \GeoJson\Geometry\LinearRing($linestring->jsonSerialize()->getCoordinates());
        }

        return new \GeoJson\Geometry\Polygon($linearrings);
    }
}
