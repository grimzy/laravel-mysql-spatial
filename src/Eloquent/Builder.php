<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Grimzy\LaravelMysqlSpatial\Types\GeometryInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    /**
     * The options to be passed to the ST_GeomFromText() function.
     * If set to false, the options argument will not be passed.
     *
     * @var string
     */
    protected $wktOptions = 'axis-order=long-lat';

    public function update(array $values)
    {
        foreach ($values as $key => &$value) {
            if ($value instanceof GeometryInterface) {
                $value = $this->asWKT($value);
            }
        }

        return parent::update($values);
    }

    protected function asWKT(GeometryInterface $geometry)
    {
        return (new SpatialExpression($geometry))->withWktOptions($this->wktOptions);
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
