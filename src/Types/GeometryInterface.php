<?php

namespace Grimzy\LaravelMysqlSpatial\Types;

interface GeometryInterface
{
    public function toWKT();

    public static function fromWKT($wkt);

    public function __toString();

    public static function fromString($wktArgument);

    public static function fromJson($geoJson);
}
