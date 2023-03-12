<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use GeoJson\GeoJson;
use GeoJson\Geometry\MultiPolygon as GeoJsonMultiPolygon;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

/**
 * @implements GeometryInterface<MultiPolygon>
 *
 * @extends GeometryCollection<Polygon>
 */
class MultiPolygon extends GeometryCollection implements GeometryInterface
{
    /**
     * The minimum number of items required to create this collection.
     */
    protected int $minimumCollectionItems = 1;

    /**
     * The class of the items in the collection.
     */
    protected string $collectionItemType = Polygon::class;

    public function toWKT(): string
    {
        return sprintf('MULTIPOLYGON(%s)', (string) $this);
    }

    public function __toString()
    {
        return implode(',', array_map(fn (Polygon $polygon) => sprintf('(%s)', (string) $polygon), $this->items));
    }

    public static function fromString(string $wktArgument, int $srid = 0): static
    {
        $parts = preg_split('/(\)\s*\)\s*,\s*\(\s*\()/', $wktArgument, -1, PREG_SPLIT_DELIM_CAPTURE);
        $polygons = static::assembleParts($parts);

        return new static(array_map(fn ($polygonString) => Polygon::fromString($polygonString), $polygons), $srid);
    }

    /**
     * Get the polygons that make up this MultiPolygon.
     */
    public function getPolygons(): array
    {
        return $this->items;
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
     */
    protected static function assembleParts(array $parts): array
    {
        $polygons = [];
        $count = count($parts);

        for ($i = 0; $i < $count; $i++) {
            if ($i % 2 !== 0) {
                [$end, $start] = explode(',', $parts[$i]);
                $polygons[$i - 1] .= $end;
                $polygons[++$i] = $start.$parts[$i];
            } else {
                $polygons[] = $parts[$i];
            }
        }

        return $polygons;
    }

    public function offsetSet($offset, $value): void
    {
        $this->validateItemType($value);

        parent::offsetSet($offset, $value);
    }

    public static function fromJson(string|GeoJson $geoJson): self
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (! is_a($geoJson, GeoJsonMultiPolygon::class)) {
            throw new InvalidGeoJsonException('Expected '.GeoJsonMultiPolygon::class.', got '.get_class($geoJson));
        }

        $set = [];
        foreach ($geoJson->getCoordinates() as $polygonCoordinates) {
            $lineStrings = [];
            foreach ($polygonCoordinates as $lineStringCoordinates) {
                $points = [];
                foreach ($lineStringCoordinates as $lineStringCoordinate) {
                    $points[] = new Point($lineStringCoordinate[1], $lineStringCoordinate[0]);
                }
                $lineStrings[] = new LineString($points);
            }
            $set[] = new Polygon($lineStrings);
        }

        return new self($set);
    }

    /**
     * Convert to GeoJson MultiPolygon that is jsonable to GeoJSON.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $polygons = [];
        foreach ($this->items as $polygon) {
            $polygons[] = $polygon->jsonSerialize();
        }

        return new GeoJsonMultiPolygon($polygons);
    }
}
