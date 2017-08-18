<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Grimzy\LaravelMysqlSpatial\Exceptions\SpatialFieldsNotDefinedException;
use Grimzy\LaravelMysqlSpatial\Types\Geometry;
use Grimzy\LaravelMysqlSpatial\Types\GeometryInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait SpatialTrait
{
    /*
     * The attributes that are spatial representations.
     * To use this Trait, add the following array to the model class
     *
     * @var array
     *
     * protected $spatialFields = [];
     */

    public $geometries = [];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     *
     * @return \Grimzy\LaravelMysqlSpatial\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    protected function performInsert(EloquentBuilder $query, array $options = [])
    {
        foreach ($this->attributes as $key => $value) {
            if ($value instanceof GeometryInterface) {
                $this->geometries[$key] = $value; //Preserve the geometry objects prior to the insert
                $this->attributes[$key] = $this->getConnection()->raw(sprintf("GeomFromText('%s')", $value->toWKT()));
            }
        }

        $insert = parent::performInsert($query, $options);

        foreach ($this->geometries as $key => $value) {
            $this->attributes[$key] = $value; //Retrieve the geometry objects so they can be used in the model
        }

        return $insert; //Return the result of the parent insert
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $spatial_fields = $this->getSpatialFields();

        foreach ($attributes as $attribute => &$value) {
            if (in_array($attribute, $spatial_fields) && is_string($value) && strlen($value) >= 15) {
                $value = Geometry::fromWKB($value);
            }
        }

        return parent::setRawAttributes($attributes, $sync);
    }

    public function getSpatialFields()
    {
        if (property_exists($this, 'spatialFields')) {
            return $this->spatialFields;
        } else {
            throw new SpatialFieldsNotDefinedException(__CLASS__.' has to define $spatialFields');
        }
    }

    public function scopeDistance($query, $distance, $geometry, $column_name, $exclude_self = false)
    {
        $query->whereRaw("st_distance(`{$column_name}`, GeomFromText('{$geometry->toWkt()}')) <= {$distance}");

        if ($exclude_self) {
            $query->whereRaw("st_distance(`{$column_name}`, GeomFromText('{$geometry->toWkt()}')) != 0");
        }

        return $query;
    }

    public function scopeDistanceSphere($query, $distance, $geometry, $column_name, $exclude_self = false)
    {
        $query->whereRaw("st_distance_sphere(`{$column_name}`, GeomFromText('{$geometry->toWkt()}')) <= {$distance}");

        if ($exclude_self) {
            $query->whereRaw("st_distance_sphere(`{$column_name}`, GeomFromText('{$geometry->toWkt()}')) != 0");
        }

        return $query;
    }

    public function scopeDistanceValue($query, $geometry, $column_name)
    {
        $columns = $query->getQuery()->columns;

        if (! $columns) {
            $query->select('*');
        }
        $query->selectRaw("st_distance(`{$column_name}`, GeomFromText('{$geometry->toWkt()}')) as distance");
    }

    public function scopeDistanceSphereValue($query, $geometry, $column_name)
    {
        $columns = $query->getQuery()->columns;

        if (! $columns) {
            $query->select('*');
        }
        $query->selectRaw("st_distance_sphere(`{$column_name}`, GeomFromText('{$geometry->toWkt()}')) as distance");
    }

    public function scopeBounding($query, Geometry $bounds, $column_name)
    {
        return $query->whereRaw("st_intersects(GeomFromText('{$bounds->toWkt()}'), `{$column_name}`)");
    }

    public function scopeComparison($query, $geometryColumn, $geometry, $relationship)
    {
        $query->whereRaw("st_{$relationship}(`{$geometryColumn}`, GeomFromText('{$geometry->toWkt()}'))");

        return $query;
    }

    public function scopeWithin($query, $geometryColumn, $polygon)
    {
        return $this->scopeComparison($query, $geometryColumn, $polygon, 'within');
    }

    public function scopeCrosses($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'crosses');
    }

    public function scopeContains($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'contains');
    }

    public function scopeDisjoint($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'disjoint');
    }

    public function scopeEquals($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'equals');
    }

    public function scopeIntersects($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'intersects');
    }

    public function scopeOverlaps($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'overlaps');
    }

    public function scopeTouches($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'touches');
    }
}