<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use NumberFormatter;
use GeoJson\GeoJson;
use GeoJson\Geometry\Point as GeoJsonPoint;
use Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException;

class Point extends Geometry
{
    protected $lat;

    protected $lng;

    private $formatter;

    public function __construct($lat, $lng)
    {
        $this->lat = (float) $lat;
        $this->lng = (float) $lng;

        $this->formatter = new NumberFormatter('en', NumberFormatter::DECIMAL);
        $this->formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, 12);
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function setLat($lat)
    {
        $this->lat = (float) $lat;
    }

    public function getLng()
    {
        return $this->lng;
    }

    public function setLng($lng)
    {
        $this->lng = (float) $lng;
    }

    public function toPair()
    {
        $lng = $this->rtrimCoordinate($this->formatter->format($this->getLng()));
        $lat = $this->rtrimCoordinate($this->formatter->format($this->getLat()));

        return $lng . ' ' . $lat;
    }

    public static function fromPair($pair)
    {
        list($lng, $lat) = explode(' ', trim($pair, "\t\n\r \x0B()"));

        return new static((float) $lat, (float) $lng);
    }

    public function toWKT()
    {
        return sprintf('POINT(%s)', (string) $this);
    }

    public static function fromString($wktArgument)
    {
        return static::fromPair($wktArgument);
    }

    public function __toString()
    {
        return $this->toPair();
    }

    /**
     * @param $geoJson  \GeoJson\Feature\Feature|string
     *
     * @return \Grimzy\LaravelMysqlSpatial\Types\Point
     */
    public static function fromJson($geoJson)
    {
        if (is_string($geoJson)) {
            $geoJson = GeoJson::jsonUnserialize(json_decode($geoJson));
        }

        if (!is_a($geoJson, GeoJsonPoint::class)) {
            throw new InvalidGeoJsonException('Expected '.GeoJsonPoint::class.', got '.get_class($geoJson));
        }

        $coordinates = $geoJson->getCoordinates();

        return new self($coordinates[1], $coordinates[0]);
    }

    /**
     * Convert to GeoJson Point that is jsonable to GeoJSON.
     *
     * @return \GeoJson\Geometry\Point
     */
    public function jsonSerialize()
    {
        return new GeoJsonPoint([$this->getLng(), $this->getLat()]);
    }

    private function rtrimCoordinate($coordinate) {
        return rtrim(rtrim($coordinate, '0'), '.');
    }
}
