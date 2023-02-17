<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Integration\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GeometryModel.
 *
 * @property int                                          id
 * @property \Grimzy\LaravelMysqlSpatial\Types\Point      location
 * @property \Grimzy\LaravelMysqlSpatial\Types\LineString line
 * @property \Grimzy\LaravelMysqlSpatial\Types\LineString shape
 */
class GeometryModel extends Model
{
    use SpatialTrait;

    protected $table = 'geometry';

    protected $spatialFields = ['location', 'line', 'multi_geometries'];
}
