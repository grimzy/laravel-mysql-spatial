<?php
namespace Grimzy\LaravelSpatial\Types;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;

abstract class PointCollection implements IteratorAggregate, Arrayable, ArrayAccess, Countable, JsonSerializable
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

    public function getPoints()
    {
        return $this->points;
    }

    public function toArray()
    {
        return $this->points;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->points);
    }

    public function prependPoint(Point $point)
    {
        array_unshift($this->points, $point);
    }

    public function appendPoint(Point $point)
    {
        $this->points[] = $point;
    }

    public function insertPoint($index, Point $point)
    {
        if (count($this->points) - 1 < $index) {
            throw new InvalidArgumentException('$index is greater than the size of the array');
        }

        array_splice($this->points, $offset, 0, [$point]);
    }

    public function offsetExists($offset)
    {
        return isset($this->points[$offset]);
    }

    /**
     * @param mixed $offset
     * @return null|Point
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->points[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (!($value instanceof Point)) {
            throw new InvalidArgumentException('$value must be an instance of Point');
        }

        if (is_null($offset)) {
            $this->appendPoint($value);
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

    public function toPairList()
    {
        return implode(',', array_map(function (Point $point) {
            return $point->toPair();
        }, $this->points));
    }
}
