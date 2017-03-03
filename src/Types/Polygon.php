<?php
namespace Grimzy\LaravelSpatial\Types;

use Countable;

class Polygon extends MultiLineString implements Countable
{

    public function toWKT()
    {
        return sprintf('POLYGON(%s)', (string)$this);
    }

    /**
     * Convert to GeoJson Polygon that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\Polygon
     */
    public function jsonSerialize()
    {
        $linearrings = [];
        foreach ($this->linestrings as $linestring) {
            $linearrings[] = new \GeoJson\Geometry\LinearRing($linestring->jsonSerialize()->getCoordinates());
        }

        return new \GeoJson\Geometry\Polygon($linearrings);
    }
}
