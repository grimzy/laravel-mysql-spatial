<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use GeoJson\GeoJson;
use GeoJson\Geometry\Polygon as GeoJsonPolygon;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

/**
 * @implements GeometryInterface<GeoJsonPolygon>
 */
class Polygon extends MultiLineString implements GeometryInterface
{
    public function toWKT(): string
    {
        return sprintf('POLYGON(%s)', (string) $this);
    }

    public static function fromJson(string|GeoJson $geoJson): self
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (! is_a($geoJson, GeoJsonPolygon::class)) {
            throw new InvalidGeoJsonException('Expected '.GeoJsonPolygon::class.', got '.get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $coordinates) {
            $points = [];
            foreach ($coordinates as $coordinate) {
                $points[] = new Point($coordinate[1], $coordinate[0]);
            }
            $set[] = new LineString($points);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson Polygon that is jsonable to GeoJSON.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $linearRings = [];
        foreach ($this->items as $lineString) {
            $linearRings[] = new \GeoJson\Geometry\LinearRing($lineString->jsonSerialize()->getCoordinates());
        }

        return new GeoJsonPolygon($linearRings);
    }
}
