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
        $linearRings = [];
        foreach ($this->items as $lineString) {
            $linearRings[] = new \GeoJson\Geometry\LinearRing($lineString->jsonSerialize()->getCoordinates());
        }

        return new \GeoJson\Geometry\Polygon($linearRings);
    }
}
