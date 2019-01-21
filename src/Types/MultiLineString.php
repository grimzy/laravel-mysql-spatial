<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiLineString as GeoJsonMultiLineString;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;
use InvalidArgumentException;

class MultiLineString extends GeometryCollection
{
    /**
     * @param LineString[] $lineStrings
     */
    public function __construct(array $lineStrings)
    {
        if (count($lineStrings) < 1) {
            throw new InvalidArgumentException('$lineStrings must contain at least one entry');
        }

        $validated = array_filter($lineStrings, function ($value) {
            return $value instanceof LineString;
        });

        if (count($lineStrings) !== count($validated)) {
            throw new InvalidArgumentException('$lineStrings must be an array of LineString');
        }

        parent::__construct($lineStrings);
    }

    public function getLineStrings()
    {
        return $this->items;
    }

    public function toWKT()
    {
        return sprintf('MULTILINESTRING(%s)', (string) $this);
    }

    public static function fromString($wktArgument)
    {
        $str = preg_split('/\)\s*,\s*\(/', substr(trim($wktArgument), 1, -1));
        $lineStrings = array_map(function ($data) {
            return LineString::fromString($data);
        }, $str);

        return new static($lineStrings);
    }

    public function __toString()
    {
        return implode(',', array_map(function (LineString $lineString) {
            return sprintf('(%s)', (string) $lineString);
        }, $this->getLineStrings()));
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof LineString)) {
            throw new InvalidArgumentException('$value must be an instance of LineString');
        }

        parent::offsetSet($offset, $value);
    }

    public static function fromJson($geoJson)
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonMultiLineString::class)) {
            throw new InvalidGeoJsonException('Expected '.GeoJsonMultiLineString::class.', got '.get_class($geoJson));
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
     * Convert to GeoJson Point that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\MultiLineString
     */
    public function jsonSerialize()
    {
        $lineStrings = [];

        foreach ($this->items as $lineString) {
            $lineStrings[] = $lineString->jsonSerialize();
        }

        return new GeoJsonMultiLineString($lineStrings);
    }
}
