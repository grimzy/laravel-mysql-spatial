<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use ArrayAccess;
use InvalidArgumentException;

abstract class PointCollection extends GeometryCollection
{
    /**
     * The class of the items in the collection.
     *
     * @var string
     */
    protected $collectionItemType = Point::class;

    public function toPairList()
    {
        return implode(',', array_map(function (Point $point) {
            return $point->toPair();
        }, $this->items));
    }

    public function offsetSet($offset, $value)
    {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    /**
     * @return array|\Grimzy\LaravelMysqlSpatial\Types\Point[]
     */
    public function getPoints()
    {
        return $this->items;
    }

    /**
     * @param \Grimzy\LaravelMysqlSpatial\Types\Point $point
     *
     * @deprecated 2.1.0 Use array_unshift($multipoint, $point); instead
     * @see array_unshift
     * @see ArrayAccess
     */
    public function prependPoint(Point $point)
    {
        array_unshift($this->items, $point);
    }

    /**
     * @param \Grimzy\LaravelMysqlSpatial\Types\Point $point
     *
     * @deprecated 2.1.0 Use $multipoint[] = $point; instead
     * @see ArrayAccess
     */
    public function appendPoint(Point $point)
    {
        $this->items[] = $point;
    }

    /**
     * @param $index
     * @param \Grimzy\LaravelMysqlSpatial\Types\Point $point
     *
     * @deprecated 2.1.0 Use array_splice($multipoint, $index, 0, [$point]); instead
     * @see array_splice
     * @see ArrayAccess
     */
    public function insertPoint($index, Point $point)
    {
        if (count($this->items) - 1 < $index) {
            throw new InvalidArgumentException('$index is greater than the size of the array');
        }

        array_splice($this->items, $index, 0, [$point]);
    }
}
