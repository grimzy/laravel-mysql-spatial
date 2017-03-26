<?php
namespace Grimzy\LaravelSpatial\Eloquent;

use Grimzy\LaravelSpatial\Exceptions\SpatialFieldsNotDefinedException;
use Grimzy\LaravelSpatial\Types\Geometry;
use Grimzy\LaravelSpatial\Types\GeometryInterface;
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
     * @return \Grimzy\LaravelSpatial\Eloquent\Builder
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

        parent::setRawAttributes($attributes, $sync);
    }

    public function getSpatialFields()
    {
        if (property_exists($this, 'spatialFields')) {
            return $this->spatialFields;
        } else {
            throw new SpatialFieldsNotDefinedException(__CLASS__ . ' has to define $spatialFields');
        }
    }

    public function scopeDistance($query, $distance, $point, $column_name) {
        // TODO: check if array, and transform to string delimited by ,
        // TODO: what about mysql 5.5? distance_sphere? need test
        return $query
          ->whereRaw("st_distance_sphere(`{$column_name}`, POINT({$point->getLng()}, {$point->getLat()})) <= {$distance}")
          ->whereRaw("st_distance_sphere(`{$column_name}`, POINT({$point->getLng()}, {$point->getLat()})) != 0");
    }

    public function scopeBounding($query, Geometry $bounds, $column_name) {
        return $query->whereRaw("MBRIntersects(GeomFromText('{$bounds->toWkt()}'), `{$column_name}`)");
    }
}