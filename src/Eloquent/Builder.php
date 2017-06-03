<?php
namespace Grimzy\LaravelSpatial\Eloquent;

use Grimzy\LaravelSpatial\Types\GeometryInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
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
        return $this->getQuery()->raw("GeomFromText('" . $geometry->toWKT() . "')");
    }
}