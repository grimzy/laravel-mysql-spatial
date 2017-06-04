<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use Countable;
use InvalidArgumentException;

class GeometryCollection extends Geometry implements Countable
{
    /**
     * @var GeometryInterface[]
     */
    protected $geometries = [];

    /**
     * @param GeometryInterface[] $geometries
     * @throws InvalidArgumentException
     */
    public function __construct(array $geometries)
    {
        $validated = array_filter($geometries, function ($value) {
            return $value instanceof GeometryInterface;
        });

        if (count($geometries) !== count($validated)) {
            throw new InvalidArgumentException('$geometries must be an array of Geometry objects');
        }

        $this->geometries = $geometries;
    }

    public function getGeometries()
    {
        return $this->geometries;
    }

    public function toWKT()
    {
        return sprintf('GEOMETRYCOLLECTION(%s)', (string) $this);
    }

    public function __toString()
    {
        return implode(',', array_map(function (GeometryInterface $geometry) {
                return $geometry->toWKT();
            }, $this->geometries));
    }

    public static function fromString($wktArgument)
    {
        $geometry_strings = preg_split('/,\s*(?=[A-Za-z])/', $wktArgument);

        return new static(array_map(function ($geometry_string) {
                $klass = Geometry::getWKTClass($geometry_string);

                return call_user_func($klass.'::fromWKT', $geometry_string);
            }, $geometry_strings));
    }

    public function count()
    {
        return count($this->geometries);
    }

    /**
     * Convert to GeoJson GeometryCollection that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\GeometryCollection
     */
    public function jsonSerialize()
    {
        $geometries = [];
        foreach ($this->geometries as $geometry) {
            $geometries[] = $geometry->jsonSerialize();
        }

        return new \GeoJson\Geometry\GeometryCollection($geometries);
    }
}
