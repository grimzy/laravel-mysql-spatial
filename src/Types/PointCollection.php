<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;

abstract class PointCollection implements IteratorAggregate, ArrayAccess, Arrayable, Countable, Jsonable, JsonSerializable
{
    /**
     * @var Point[]
     */
    protected $points;

    /**
     * @param Point[] $points
     */
    public function __construct(array $points)
    {
        if (count($points) < 2) {
            throw new InvalidArgumentException('$points must contain at least two entries');
        }

        $validated = array_filter($points, function ($value) {
            return $value instanceof Point;
        });

        if (count($points) !== count($validated)) {
            throw new InvalidArgumentException('$points must be an array of Points');
        }
        $this->points = $points;
    }

    public function toArray()
    {
        return $this->points;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->points);
    }

    public function offsetExists($offset)
    {
        return isset($this->points[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->points[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (! ($value instanceof Point)) {
            throw new InvalidArgumentException('$value must be an instance of Point');
        }

        if (is_null($offset)) {
            $this->points[] = $value;
        } else {
            $this->points[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->points[$offset]);
    }

    public function count()
    {
        return count($this->points);
    }

    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    public function toPairList()
    {
        return implode(',', array_map(function (Point $point) {
            return $point->toPair();
        }, $this->points));
    }

    /**
     * @return array|\Grimzy\LaravelMysqlSpatial\Types\Point[]
     *
     * @deprecated 2.1.0 Use $multipoint->toArray() instead
     *
     * @see IteratorAggregate
     * @see ArrayAccess
     * @see Arrayable
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param \Grimzy\LaravelMysqlSpatial\Types\Point $point
     *
     * @deprecated 2.1.0 Use array_unshift($multipoint, $point); instead
     *
     * @see array_unshift
     * @see ArrayAccess
     */
    public function prependPoint(Point $point)
    {
        array_unshift($this->points, $point);
    }

    /**
     * @param \Grimzy\LaravelMysqlSpatial\Types\Point $point
     *
     * @deprecated 2.1.0 Use $multipoint[] = $point; instead
     *
     * @see ArrayAccess
     */
    public function appendPoint(Point $point)
    {
        $this->points[] = $point;
    }

    /**
     * @param $index
     * @param \Grimzy\LaravelMysqlSpatial\Types\Point $point
     *
     * @deprecated 2.1.0 Use array_splice($multipoint, $index, 0, [$point]); instead
     *
     * @see array_splice
     * @see ArrayAccess
     */
    public function insertPoint($index, Point $point)
    {
        if (count($this->points) - 1 < $index) {
            throw new InvalidArgumentException('$index is greater than the size of the array');
        }

        array_splice($this->points, $index, 0, [$point]);
    }
}
