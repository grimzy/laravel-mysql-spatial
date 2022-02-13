<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Illuminate\Database\Query\Expression;

class SpatialExpression extends Expression
{
    /**
     * The options to be passed to the ST_GeomFromText() function.
     * If set to false, the options argument will not be passed.
     *
     * @var string
     */
    protected $wktOptions = 'axis-order=long-lat';

    public function getValue()
    {
        $thirdArgument = $this->wktOptions ? ", '$this->wktOptions'" : '';

        return "ST_GeomFromText(?, ?$thirdArgument)";
    }

    public function getSpatialValue()
    {
        return $this->value->toWkt();
    }

    public function getSrid()
    {
        return $this->value->getSrid();
    }

    /**
     * Set the WKT options.
     *
     * @param string $wktOptions
     *
     * @return self
     */
    public function withWktOptions($wktOptions)
    {
        $this->wktOptions = $wktOptions;

        return $this;
    }
}
