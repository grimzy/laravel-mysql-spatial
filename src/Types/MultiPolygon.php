<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use Countable;
use InvalidArgumentException;

class MultiPolygon extends Geometry implements Countable
{
    /**
     * @var Polygon[]
     */
    protected $polygons;

    /**
     * @param Polygon[] $polygons
     */
    public function __construct(array $polygons)
    {
        $validated = array_filter($polygons, function ($value) {
            return $value instanceof Polygon;
        });

        if (count($polygons) !== count($validated)) {
            throw new InvalidArgumentException('$polygons must be an array of Points');
        }
        $this->polygons = $polygons;
    }

    public function toWKT()
    {
        return sprintf('MULTIPOLYGON(%s)', (string) $this);
    }

    public function __toString()
    {
        return implode(',', array_map(function (Polygon $polygon) {
            return sprintf('(%s)', (string) $polygon);
        }, $this->polygons));
    }

    public static function fromString($wktArgument)
    {
        $parts = preg_split('/(\)\s*\)\s*,\s*\(\s*\()/', $wktArgument, -1, PREG_SPLIT_DELIM_CAPTURE);
        $polygons = static::assembleParts($parts);

        return new static(array_map(function ($polygonString) {
            return Polygon::fromString($polygonString);
        }, $polygons));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     *             </p>
     *             <p>
     *             The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->polygons);
    }

    /**
     * Get the polygons that make up this MultiPolygon.
     *
     * @return array|Polygon[]
     */
    public function getPolygons()
    {
        return $this->polygons;
    }

    /**
     * Make an array like this:
     * "((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1",
     * ")), ((",
     * "-1 -1,-1 -2,-2 -2,-2 -1,-1 -1",
     * ")), ((",
     * "-1 -1,-1 -2,-2 -2,-2 -1,-1 -1))".
     *
     * Into:
     * "((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1))",
     * "((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1))",
     * "((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1))"
     *
     * @param array $parts
     *
     * @return array
     */
    protected static function assembleParts(array $parts)
    {
        $polygons = [];
        $count = count($parts);

        for ($i = 0; $i < $count; $i++) {
            if ($i % 2 !== 0) {
                list($end, $start) = explode(',', $parts[$i]);
                $polygons[$i - 1] .= $end;
                $polygons[++$i] = $start.$parts[$i];
            } else {
                $polygons[] = $parts[$i];
            }
        }

        return $polygons;
    }

    /**
     * Convert to GeoJson MultiPolygon that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\MultiPolygon
     */
    public function jsonSerialize()
    {
        $polygons = [];
        foreach ($this->polygons as $polygon) {
            $polygons[] = $polygon->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiPolygon($polygons);
    }
}
