<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPoint as GeoJsonMultiPoint;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

class MultiPoint extends PointCollection
{
    /**
     * The minimum number of items required to create this collection.
     *
     * @var int
     */
    protected $minimumCollectionItems = 1;

    public function toWKT()
    {
        return sprintf('MULTIPOINT(%s)', (string) $this);
    }

    public static function fromWkt($wkt)
    {
        $wktArgument = Geometry::getWKTArgument($wkt);

        return static::fromString($wktArgument);
    }

    public static function fromString($wktArgument)
    {
        $matches = [];
        preg_match_all('/\(\s*(\d+\s+\d+)\s*\)/', trim($wktArgument), $matches);

        $points = array_map(function ($pair) {
            return Point::fromPair($pair);
        }, $matches[1]);

        return new static($points);
    }

    public function __toString()
    {
        return implode(',', array_map(function (Point $point) {
            return sprintf('(%s)', $point->toPair());
        }, $this->items));
    }

    public static function fromJson($geoJson)
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonMultiPoint::class)) {
            throw new InvalidGeoJsonException('Expected '.GeoJsonMultiPoint::class.', got '.get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $coordinate) {
            $set[] = new Point($coordinate[1], $coordinate[0]);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson MultiPoint that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\MultiPoint
     */
    public function jsonSerialize()
    {
        $points = [];
        foreach ($this->items as $point) {
            $points[] = $point->jsonSerialize();
        }

        return new GeoJsonMultiPoint($points);
    }
}
