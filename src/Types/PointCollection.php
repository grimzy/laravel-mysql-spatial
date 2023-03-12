<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

/**
 * @extends GeometryCollection<Point>
 */
abstract class PointCollection extends GeometryCollection
{
    /**
     * The class of the items in the collection.
     */
    protected string $collectionItemType = Point::class;

    public function toPairList(): string
    {
        return implode(',', array_map(fn (Point $point): string => $point->toPair(), $this->items));
    }

    public function offsetSet($offset, $value): void
    {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    public function getPoints(): array
    {
        return $this->items;
    }
}
