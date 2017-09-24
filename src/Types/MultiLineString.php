<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use InvalidArgumentException;

class MultiLineString extends GeometryCollection
{
    /**
     * @param LineString[] $linestrings
     */
    public function __construct(array $linestrings)
    {
        if (count($linestrings) < 1) {
            throw new InvalidArgumentException('$linestrings must contain at least one entry');
        }

        $validated = array_filter($linestrings, function ($value) {
            return $value instanceof LineString;
        });

        if (count($linestrings) !== count($validated)) {
            throw new InvalidArgumentException('$linestrings must be an array of LineString');
        }

        parent::__construct($linestrings);
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
        $linestrings = array_map(function ($data) {
            return LineString::fromString($data);
        }, $str);

        return new static($linestrings);
    }

    public function __toString()
    {
        return implode(',', array_map(function (LineString $linestring) {
            return sprintf('(%s)', (string) $linestring);
        }, $this->getLineStrings()));
    }

    public function offsetSet($offset, $value)
    {
        if (! ($value instanceof LineString)) {
            throw new InvalidArgumentException('$value must be an instance of LineString');
        }

        parent::offsetSet($offset, $value);
    }

    /**
     * Convert to GeoJson Point that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\MultiLineString
     */
    public function jsonSerialize()
    {
        $linestrings = [];

        foreach ($this->items as $linestring) {
            $linestrings[] = $linestring->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiLineString($linestrings);
    }
}
