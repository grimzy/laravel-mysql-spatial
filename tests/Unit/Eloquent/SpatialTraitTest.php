<?php

use Grimzy\LaravelSpatial\MysqlConnection;
use Grimzy\LaravelSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Mockery as m;

class SpatialTraitTest extends BaseTestCase
{
    /**
     * @var TestModel
     */
    protected $model;

    /**
     * @var array
     */
    protected $queries;

    public function setUp()
    {
        $this->model = new TestModel();
        $this->queries = &$this->model->getConnection()->getPdo()->queries;
    }

    public function tearDown()
    {
        $this->model->getConnection()->getPdo()->resetQueries();
    }

    public function testInsertPointHasCorrectSql()
    {
        $this->model->point = new Point(1, 2);
        $this->model->save();

        $this->assertContains("ST_GeomFromText('POINT(2 1)')", $this->queries[0]);
    }

    public function testUpdatePointHasCorrectSql()
    {
        $this->model->exists = true;
        $this->model->point = new Point(2, 4);
        $this->model->save();

        $this->assertContains("ST_GeomFromText('POINT(4 2)')", $this->queries[0]);
    }
}

class TestModel extends Model
{
    use \Grimzy\LaravelSpatial\Eloquent\SpatialTrait;

    protected $postgisFields = [
        'point' => Point::class
    ];


    public static $pdo;

    public static function resolveConnection($connection = null)
    {
        if (is_null(static::$pdo)) {
            static::$pdo = m::mock('TestPDO')->makePartial();
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

class TestRelatedModel extends TestModel
{
    public function testmodel()
    {
        return $this->belongsTo(TestModel::class);
    }

    public function testmodels()
    {
        return $this->belongsToMany(TestModel::class);
    }
}

class TestPDO extends PDO
{
    public $queries = [];
    public $counter = 1;

    public function prepare($statement, $driver_options = null)
    {
        $this->queries[] = $statement;

        $stmt = m::mock('PDOStatement');
        $stmt->shouldReceive('bindValue')->zeroOrMoreTimes();
        $stmt->shouldReceive('execute');
        $stmt->shouldReceive('fetchAll')->andReturn([['id' => 1, 'point' => 'POINT(1 2)']]);
        $stmt->shouldReceive('rowCount')->andReturn(1);

        return $stmt;
    }

    public function lastInsertId($name = null)
    {
        return $this->counter++;
    }

    public function resetQueries()
    {
        $this->queries = [];
    }
}
