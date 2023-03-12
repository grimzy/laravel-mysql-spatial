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

/**
 * @template G
 *
 * @implements GeometryInterface<FeatureCollection>
 */
class GeometryCollection extends Geometry implements IteratorAggregate, ArrayAccess, Arrayable, Countable, GeometryInterface
{
    /**
     * The minimum number of items required to create this collection.
     */
    protected int $minimumCollectionItems = 0;

    /**
     * The class of the items in the collection.
     */
    protected string $collectionItemType = GeometryInterface::class;

    /**
     * The items contained in the spatial collection.
     *
     * @var G[]
     */
    protected array $items = [];

    /**
     * @param  GeometryInterface[]  $geometries
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $geometries, ?int $srid = 0)
    {
        parent::__construct((int) $srid);

        $this->validateItems($geometries);

        $this->items = $geometries;
    }

    /**
     * @return G[]
     */
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
        return implode(',', array_map(fn (GeometryInterface $geometry) => $geometry->toWKT(), $this->items));
    }

    public static function fromString(string $wktArgument, int $srid = 0): static
    {
        if (empty($wktArgument)) {
            return new static([]);
        }

        $geometry_strings = preg_split('/,\s*(?=[A-Za-z])/', $wktArgument);

        return new static(array_map(function ($geometry_string) {
            $klass = Geometry::getWKTClass($geometry_string);

            return call_user_func($klass.'::fromWKT', $geometry_string);
        }, $geometry_strings), $srid);
    }

    public function toArray()
    {
        return $this->items;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->items[$offset] : null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->validateItemType($value);

        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public static function fromJson(string|GeoJson $geoJson): self
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (! is_a($geoJson, FeatureCollection::class)) {
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
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $geometries = [];
        foreach ($this->items as $geometry) {
            $geometries[] = $geometry->jsonSerialize();
        }

        return new \GeoJson\Geometry\GeometryCollection($geometries);
    }

    /**
     * Checks whether the items are valid to create this collection.
     */
    protected function validateItems(array $items): void
    {
        $this->validateItemCount($items);

        foreach ($items as $item) {
            $this->validateItemType($item);
        }
    }

    /**
     * Checks whether the array has enough items to generate a valid WKT.
     *
     * @param  GeometryInterface[]  $items
     *
     * @see $minimumCollectionItems
     */
    protected function validateItemCount(array $items): void
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
     *
     * @see $collectionItemType
     */
    protected function validateItemType(mixed $item): void
    {
        if (! $item instanceof $this->collectionItemType) {
            throw new InvalidArgumentException(sprintf(
                '%s must be a collection of %s',
                get_class($this),
                $this->collectionItemType
            ));
        }
    }
}
