<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Integration\Eloquent;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use \Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

    protected $spatialFields = ['point'];   // TODO: only required when fetching, not saving

    public $timestamps = false;

    public static $pdo;

    public function testrelatedmodels()
    {
        return $this->hasMany(TestRelatedModel::class);
    }

    public function testrelatedmodels2()
    {
        return $this->belongsToMany(TestRelatedModel::class);
    }
}
