<?php

namespace Grimzy\LaravelMysqlSpatial\Eloquent;

use Grimzy\LaravelMysqlSpatial\Exceptions\SpatialFieldsNotDefinedException;
use Grimzy\LaravelMysqlSpatial\Exceptions\UnknownSpatialRelationFunction;
use Grimzy\LaravelMysqlSpatial\Types\Geometry;
use Grimzy\LaravelMysqlSpatial\Types\GeometryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait SpatialTrait.
 *
 * @method static distance($geometryColumn, $geometry, $distance)
 * @method static distanceExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static distanceSphere($geometryColumn, $geometry, $distance)
 * @method static distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)
 * @method static comparison($geometryColumn, $geometry, $relationship)
 * @method static within($geometryColumn, $polygon)
 * @method static crosses($geometryColumn, $geometry)
 * @method static contains($geometryColumn, $geometry)
 * @method static disjoint($geometryColumn, $geometry)
 * @method static equals($geometryColumn, $geometry)
 * @method static intersects($geometryColumn, $geometry)
 * @method static overlaps($geometryColumn, $geometry)
 * @method static doesTouch($geometryColumn, $geometry)
 */
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

    protected $stRelations = [
        'within',
        'crosses',
        'contains',
        'disjoint',
        'equals',
        'intersects',
        'overlaps',
        'touches',
    ];

    public static function bootSpatialTrait()
    {
        static::saving(function (Model $model) {
            foreach ($model->attributes as $key => $value) {
                if ($value instanceof GeometryInterface) {
                    $model->geometries[$key] = $value; //Preserve the geometry objects prior to the insert
                    $model->attributes[$key] = new SpatialExpression($value);
                }
            }
        });

        $saved = function (Model $model) {
            $model->attributes = array_merge($model->attributes, $model->geometries);
        };

        static::updated($saved);
        static::created($saved);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return \Grimzy\LaravelMysqlSpatial\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new BaseBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor()
        );
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

    public function isColumnAllowed($geometryColumn)
    {
        if (!in_array($geometryColumn, $this->getSpatialFields())) {
            throw new SpatialFieldsNotDefinedException();
        }

        return true;
    }

    public function scopeDistance($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query->whereRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?)) <= ?", [
            $geometry->toWkt(),
            $distance,
        ]);

        return $query;
    }

    public function scopeDistanceExcludingSelf($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query = $this->scopeDistance($query, $geometryColumn, $geometry, $distance);

        $query->whereRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?)) != 0", [
            $geometry->toWkt(),
        ]);

        return $query;
    }

    public function scopeDistanceValue($query, $geometryColumn, $geometry)
    {
        $this->isColumnAllowed($geometryColumn);

        $columns = $query->getQuery()->columns;

        if (!$columns) {
            $query->select('*');
        }

        $query->selectRaw("st_distance(`$geometryColumn`, ST_GeomFromText(?)) as distance", [
            $geometry->toWkt(),
        ]);
    }

    public function scopeDistanceSphere($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query->whereRaw("st_distance_sphere(`$geometryColumn`, ST_GeomFromText(?)) <= ?", [
            $geometry->toWkt(),
            $distance,
        ]);

        return $query;
    }

    public function scopeDistanceSphereExcludingSelf($query, $geometryColumn, $geometry, $distance)
    {
        $this->isColumnAllowed($geometryColumn);

        $query = $this->scopeDistanceSphere($query, $geometryColumn, $geometry, $distance);

        $query->whereRaw("st_distance_sphere($geometryColumn, ST_GeomFromText(?)) != 0", [
            $geometry->toWkt(),
        ]);

        return $query;
    }

    public function scopeDistanceSphereValue($query, $geometryColumn, $geometry)
    {
        $this->isColumnAllowed($geometryColumn);

        $columns = $query->getQuery()->columns;

        if (!$columns) {
            $query->select('*');
        }
        $query->selectRaw("st_distance_sphere(`$geometryColumn`, ST_GeomFromText(?)) as distance", [
            $geometry->toWkt(),
        ]);
    }

    public function scopeComparison($query, $geometryColumn, $geometry, $relationship)
    {
        $this->isColumnAllowed($geometryColumn);

        if (!in_array($relationship, $this->stRelations)) {
            throw new UnknownSpatialRelationFunction($relationship);
        }

        $query->whereRaw("st_{$relationship}(`$geometryColumn`, ST_GeomFromText(?))", [
            $geometry->toWkt(),
        ]);

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

    public function scopeDoesTouch($query, $geometryColumn, $geometry)
    {
        return $this->scopeComparison($query, $geometryColumn, $geometry, 'touches');
    }
}
