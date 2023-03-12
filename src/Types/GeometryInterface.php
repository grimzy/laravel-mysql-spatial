<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

use GeoJson\GeoJson;

/**
 * @template T
 */
interface GeometryInterface
{
    public function toWKT(): string;

    public static function fromWKT(string $wkt, int $srid = 0): self;

    public function __toString();

    public static function fromString(string $wktArgument, int $srid = 0): self;

    public static function fromJson(string|GeoJson $geoJson): self;

    /**
     * @return T
     */
    public function jsonSerialize();

    public function getSrid(): int;
}
