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
use ReturnTypeWillChange;

class GeometryCollection extends Geometry implements IteratorAggregate, ArrayAccess, Arrayable, Countable
{
    /**
     * The minimum number of items required to create this collection.
     *
     * @var int
     */
    protected int $minimumCollectionItems = 0;

    /**
     * The class of the items in the collection.
     *
     * @var string
     */
    protected string $collectionItemType = GeometryInterface::class;

    /**
     * The items contained in the spatial collection.
     *
     * @var GeometryInterface[]
     */
    protected array $items = [];

    /**
     * @param GeometryInterface[] $geometries
     * @param int $srid
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $geometries, int $srid = 0)
    {
        parent::__construct($srid);

        $this->validateItems($geometries);

        $this->items = $geometries;
    }

    public function getGeometries(): array
    {
        return $this->items;
    }

    public function toWKT(): string
    {
        return sprintf('GEOMETRYCOLLECTION(%s)', (string) $this);
    }

    public function __toString()
    {
        return implode(',', array_map(function (GeometryInterface $geometry) {
            return $geometry->toWKT();
        }, $this->items));
    }

    public static function fromString($wktArgument, $srid = 0): static
    {
        if (empty($wktArgument)) {
            return new static([]);
        }

        $geometryStrings = preg_split('/,\s*(?=[A-Za-z])/', $wktArgument);

        return new static(
            array_map(
                function ($geometryString) {
                    $klass = Geometry::getWKTClass($geometryString);

                    return call_user_func($klass.'::fromWKT', $geometryString);
                },
                $geometryStrings
            ),
            $srid
        );
    }

    public function toArray(): array
    {
        return $this->items;
    }

    #[ReturnTypeWillChange] public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    #[ReturnTypeWillChange] public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    #[ReturnTypeWillChange] public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->items[$offset] : null;
    }

    #[ReturnTypeWillChange] public function offsetSet($offset, $value)
    {
        $this->validateItemType($value);

        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    #[ReturnTypeWillChange] public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    #[ReturnTypeWillChange] public function count()
    {
        return count($this->items);
    }

    public static function fromJson($geoJson): GeometryCollection
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
    #[ReturnTypeWillChange] public function jsonSerialize()
    {
        $geometries = [];
        foreach ($this->items as $geometry) {
            $geometries[] = $geometry->jsonSerialize();
        }

        return new \GeoJson\Geometry\GeometryCollection($geometries);
    }

    /**
     * Checks whether the items are valid to create this collection.
     *
     * @param array $items
     */
    protected function validateItems(array $items)
    {
        $this->validateItemCount($items);

        foreach ($items as $item) {
            $this->validateItemType($item);
        }
    }

    /**
     * Checks whether the array has enough items to generate a valid WKT.
     *
     * @param array $items
     *
     * @see $minimumCollectionItems
     */
    protected function validateItemCount(array $items)
    {
        if (count($items) < $this->minimumCollectionItems) {
            $entries = $this->minimumCollectionItems === 1 ? 'entry' : 'entries';

            throw new InvalidArgumentException(sprintf(
                '%s must contain at least %d %s',
                get_class($this),
                $this->minimumCollectionItems,
                $entries
            ));
        }
    }

    /**
     * Checks the type of the items in the array.
     *
     * @param $item
     *
     * @see $collectionItemType
     */
    protected function validateItemType($item)
    {
        if (!$item instanceof $this->collectionItemType) {
            throw new InvalidArgumentException(sprintf(
                '%s must be a collection of %s',
                get_class($this),
                $this->collectionItemType
            ));
        }
    }
}
