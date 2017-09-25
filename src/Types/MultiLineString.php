<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

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

        return new \GeoJson\Geometry\MultiLineString($lineStrings);
    }
}
