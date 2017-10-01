<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use ArrayAccess;
use ArrayIterator;
use Countable;
use GeoJson\Feature\FeatureCollection;
use GeoJson\GeoJson;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use IteratorAggregate;

class GeometryCollection extends Geometry implements IteratorAggregate, ArrayAccess, Arrayable, Countable
{
    /**
     * The items contained in the spatial collection.
     *
     * @var GeometryInterface[]
     */
    protected $items = [];

    /**
     * @param GeometryInterface[] $geometries
     *
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

        $this->items = $geometries;
    }

    public function getGeometries()
    {
        return $this->items;
    }

    public function toWKT()
    {
        return sprintf('GEOMETRYCOLLECTION(%s)', (string) $this);
    }

    public function __toString()
    {
        return implode(',', array_map(function (GeometryInterface $geometry) {
            return $geometry->toWKT();
        }, $this->items));
    }

    public static function fromString($wktArgument)
    {
        $geometry_strings = preg_split('/,\s*(?=[A-Za-z])/', $wktArgument);

        return new static(array_map(function ($geometry_string) {
            $klass = Geometry::getWKTClass($geometry_string);

            return call_user_func($klass.'::fromWKT', $geometry_string);
        }, $geometry_strings));
    }

    public function toArray()
    {
        return $this->items;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->items[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof GeometryInterface)) {
            throw new InvalidArgumentException('$value must be an instance of GeometryInterface');
        }

        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function count()
    {
        return count($this->items);
    }

    public static function fromJson($geoJson)
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, FeatureCollection::class)) {
            throw new InvalidGeoJsonException('Expected '.FeatureCollection::class.', got '.get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getFeatures() as $feature) {
            $set[] = parent::fromJson($feature);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson GeometryCollection that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\GeometryCollection
     */
    public function jsonSerialize()
    {
        $geometries = [];
        foreach ($this->items as $geometry) {
            $geometries[] = $geometry->jsonSerialize();
        }

        return new \GeoJson\Geometry\GeometryCollection($geometries);
    }
}
