<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Grimzy\LaravelMysqlSpatial\Types\Geometry;
use Grimzy\LaravelMysqlSpatial\Types\GeometryInterface;
use Illuminate\Database\Grammar;
use Illuminate\Database\Query\Expression;

class SpatialExpression extends Expression
{
    /**
     * @var Geometry|GeometryInterface
     */
    protected $value;

    /**
     * @param  Geometry|GeometryInterface  $value
     * @return void
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue(Grammar $grammar)
    {
        return "ST_GeomFromText(?, ?, 'axis-order=long-lat')";
    }

    public function getSpatialValue(): string
    {
        return $this->value->toWkt();
    }

    public function getSrid(): int
    {
        return $this->value->getSrid();
    }

    public function toWkt(): string
    {
        return $this->value->toWkt();
    }
}
