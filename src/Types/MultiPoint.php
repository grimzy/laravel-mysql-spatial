<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPoint as GeoJsonMultiPoint;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

/**
 * @implements GeometryInterface<Point>
 */
class MultiPoint extends PointCollection implements GeometryInterface
{
    /**
     * The minimum number of items required to create this collection.
     */
    protected int $minimumCollectionItems = 1;

    public function toWKT(): string
    {
        return sprintf('MULTIPOINT(%s)', (string) $this);
    }

    public static function fromWKT(string $wkt, int $srid = 0): static
    {
        $wktArgument = Geometry::getWKTArgument($wkt);

        return static::fromString($wktArgument, $srid);
    }

    public static function fromString(string $wktArgument, int $srid = 0): static
    {
        $matches = [];
        preg_match_all('/\(\s*(\d+\s+\d+)\s*\)/', trim($wktArgument), $matches);

        $points = array_map(fn ($pair) => Point::fromPair($pair), $matches[1]);

        return new static($points, $srid);
    }

    public function __toString()
    {
        return implode(',', array_map(fn (Point $point) => sprintf('(%s)', $point->toPair()), $this->items));
    }

    public static function fromJson(string|GeoJson $geoJson): self
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (! is_a($geoJson, GeoJsonMultiPoint::class)) {
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
