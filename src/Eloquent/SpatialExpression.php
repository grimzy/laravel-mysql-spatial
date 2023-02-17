<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Illuminate\Database\Query\Expression;

class SpatialExpression extends Expression
{
    public function getValue()
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
}
