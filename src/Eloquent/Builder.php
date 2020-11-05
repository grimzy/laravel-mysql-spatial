<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Grimzy\LaravelMysqlSpatial\Exceptions\UnknownSpatialFunctionException;
use Grimzy\LaravelMysqlSpatial\Exceptions\UnknownSpatialRelationFunction;
use Grimzy\LaravelMysqlSpatial\Types\GeometryInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Builder extends EloquentBuilder
{
    /**
     * @var SpatialTrait
     */
    protected $model;

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
        return new SpatialExpression($geometry);
    }

    public function distance($geometryColumn, $geometry, $distance)
    {
        $this->model->isColumnAllowed($geometryColumn);

        $this->whereRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat')) <= ?", [
            $geometry->toWkt(),
            $geometry->getSrid(),
            $distance,
        ]);

        return $this;
    }

    public function distanceExcludingSelf($geometryColumn, $geometry, $distance)
    {
        $this->model->isColumnAllowed($geometryColumn);

        $this->distance($geometryColumn, $geometry, $distance);

        $this->whereRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat')) != 0", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $this;
    }

    public function distanceValue($geometryColumn, $geometry)
    {
        $this->model->isColumnAllowed($geometryColumn);

        $columns = $this->getQuery()->columns;

        if (!$columns) {
            $this->select('*');
        }

        $this->selectRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat')) as distance", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $this;
    }

    public function distanceSphere($geometryColumn, $geometry, $distance)
    {
        $this->model->isColumnAllowed($geometryColumn);

        $this->whereRaw("st_distance_sphere(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat')) <= ?", [
            $geometry->toWkt(),
            $geometry->getSrid(),
            $distance,
        ]);

        return $this;
    }

    public function distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)
    {
        $this->model->isColumnAllowed($geometryColumn);

        $this->distanceSphere($geometryColumn, $geometry, $distance);

        $this->whereRaw("st_distance_sphere($geometryColumn, ST_GeomFromText(?, ?, 'axis-order=long-lat')) != 0", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $this;
    }

    public function distanceSphereValue($geometryColumn, $geometry)
    {
        $this->model->isColumnAllowed($geometryColumn);

        $columns = $this->getQuery()->columns;

        if (!$columns) {
            $this->select('*');
        }
        $this->selectRaw("st_distance_sphere(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat')) as distance", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $this;
    }

    public function comparison($geometryColumn, $geometry, $relationship)
    {
        $this->model->isColumnAllowed($geometryColumn);

        if (!in_array($relationship, $this->model->getStRelations())) {
            throw new UnknownSpatialRelationFunction($relationship);
        }

        $this->whereRaw("st_{$relationship}(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat'))", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $this;
    }

    public function within($geometryColumn, $polygon)
    {
        return $this->comparison($geometryColumn, $polygon, 'within');
    }

    public function crosses($geometryColumn, $geometry)
    {
        return $this->comparison($geometryColumn, $geometry, 'crosses');
    }

    public function contains($geometryColumn, $geometry)
    {
        return $this->comparison($geometryColumn, $geometry, 'contains');
    }

    public function disjoint($geometryColumn, $geometry)
    {
        return $this->comparison($geometryColumn, $geometry, 'disjoint');
    }

    public function equals($geometryColumn, $geometry)
    {
        return $this->comparison($geometryColumn, $geometry, 'equals');
    }

    public function intersects($geometryColumn, $geometry)
    {
        return $this->comparison($geometryColumn, $geometry, 'intersects');
    }

    public function overlaps($geometryColumn, $geometry)
    {
        return $this->comparison($geometryColumn, $geometry, 'overlaps');
    }

    public function doesTouch($geometryColumn, $geometry)
    {
        return $this->comparison($geometryColumn, $geometry, 'touches');
    }

    public function orderBySpatial($geometryColumn, $geometry, $orderFunction, $direction = 'asc')
    {
        $this->model->isColumnAllowed($geometryColumn);

        if (!in_array($orderFunction, $this->model->getStOrderFunctions())) {
            throw new UnknownSpatialFunctionException($orderFunction);
        }

        $this->orderByRaw("st_{$orderFunction}(`$geometryColumn`, ST_GeomFromText(?, ?, 'axis-order=long-lat')) {$direction}", [
            $geometry->toWkt(),
            $geometry->getSrid(),
        ]);

        return $this;
    }

    public function orderByDistance($geometryColumn, $geometry, $direction = 'asc')
    {
        return $this->orderBySpatial($geometryColumn, $geometry, 'distance', $direction);
    }

    public function orderByDistanceSphere($geometryColumn, $geometry, $direction = 'asc')
    {
        return $this->orderBySpatial($geometryColumn, $geometry, 'distance_sphere', $direction);
    }
}
