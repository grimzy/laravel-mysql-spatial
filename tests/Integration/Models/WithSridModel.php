<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Integration\Models;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WithSridModel.
 *
 * @property int                                          id
 * @property \Grimzy\LaravelMysqlSpatial\Types\Point      location
 * @property \Grimzy\LaravelMysqlSpatial\Types\LineString line
 * @property \Grimzy\LaravelMysqlSpatial\Types\LineString shape
 */
class WithSridModel extends Model
{
    use SpatialTrait;

    protected $table = 'with_srid';

    protected $spatialFields = ['location', 'line'];

    public $timestamps = false;
}
