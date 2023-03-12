<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use GeoJson\GeoJson;
use GeoJson\Geometry\Point as GeoJsonPoint;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

/**
 * @implements GeometryInterface<GeoJsonPoint>
 */
class Point extends Geometry implements GeometryInterface
{
    protected float $lat;

    protected float $lng;

    public function __construct(float $lat, float $lng, ?int $srid = 0)
    {
        parent::__construct((int) $srid);

        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function setLat(float $lat): void
    {
        $this->lat = $lat;
    }

    public function getLng(): float
    {
        return $this->lng;
    }

    public function setLng(float $lng): void
    {
        $this->lng = $lng;
    }

    public function toPair(): string
    {
        return $this->getLng().' '.$this->getLat();
    }

    public static function fromPair(string $pair, int $srid = 0): static
    {
        [$lng, $lat] = explode(' ', trim($pair, "\t\n\r \x0B()"));

        return new static((float) $lat, (float) $lng, $srid);
    }

    public function toWKT(): string
    {
        return sprintf('POINT(%s)', (string) $this);
    }

    public static function fromString(string $wktArgument, int $srid = 0): self
    {
        return static::fromPair($wktArgument, $srid);
    }

    public function __toString(): string
    {
        return $this->getLng().' '.$this->getLat();
    }

    /**
     * @param $geoJson  \GeoJson\Feature\Feature|string
     * @return \Grimzy\LaravelMysqlSpatial\Types\Point
     */
    public static function fromJson(string|GeoJson $geoJson): self
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (! is_a($geoJson, GeoJsonPoint::class)) {
            throw new InvalidGeoJsonException('Expected '.GeoJsonPoint::class.', got '.get_class($geoJson));
        }

        $coordinates = $geoJson->getCoordinates();

        return new self($coordinates[1], $coordinates[0]);
    }

    /**
     * Convert to GeoJson Point that is jsonable to GeoJSON.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return new GeoJsonPoint([$this->getLng(), $this->getLat()]);
    }
}
