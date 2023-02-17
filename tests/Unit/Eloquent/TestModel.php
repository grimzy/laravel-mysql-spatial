<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Unit\Eloquent;

use Grimzy\LaravelMysqlSpatial\MysqlConnection;
use Illuminate\Database\Eloquent\Model;
use Mockery as m;

class TestModel extends Model
{
    use \Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

    protected $spatialFields = ['point'];   // TODO: only required when fetching, not saving

    public $timestamps = false;

    public static $pdo;

    public static function resolveConnection($connection = null)
    {
        if (is_null(static::$pdo)) {
            static::$pdo = m::mock(TestPDO::class)->makePartial();
        }

        return new MysqlConnection(static::$pdo);
    }

    public function testrelatedmodels()
    {
        return $this->hasMany(TestRelatedModel::class);
    }

    public function testrelatedmodels2()
    {
        return $this->belongsToMany(TestRelatedModel::class);
    }
}
