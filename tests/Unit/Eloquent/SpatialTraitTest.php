<?php

use Grimzy\LaravelMysqlSpatial\MysqlConnection;
use Grimzy\LaravelMysqlSpatial\Types\Point;
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

    public function testInsertUpdatePointHasCorrectSql()
    {
        $this->assertFalse($this->model->exists);

        $this->model->point = new Point(1, 2);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertContains("ST_GeomFromText('POINT(2 1)')", $this->queries[0]);
        $this->assertTrue($this->model->exists);

        $this->model->point = new Point(1, 2);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertContains("ST_GeomFromText('POINT(2 1)')", $this->queries[1]);
    }

    public function testInsertUpdateLineStringHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);

        $this->assertFalse($this->model->exists);

        $this->model->linestring = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertContains("ST_GeomFromText('LINESTRING(2 1,3 2)')", $this->queries[0]);
        $this->assertTrue($this->model->exists);

        $this->model->linestring = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertContains("ST_GeomFromText('LINESTRING(2 1,3 2)')", $this->queries[1]);
    }

    public function testInsertUpdatePolygonHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $linestring1 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);
        $linestring2 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point3, $point4]);

        $this->assertFalse($this->model->exists);

        $this->model->polygon = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([$linestring1, $linestring2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertContains("ST_GeomFromText('POLYGON((2 1,3 2),(2 3,1 2))')", $this->queries[0]);
        $this->assertTrue($this->model->exists);

        $this->model->polygon = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([$linestring1, $linestring2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertContains("ST_GeomFromText('POLYGON((2 1,3 2),(2 3,1 2))')", $this->queries[1]);
    }

    public function testInsertUpdateMultiPointHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);

        $this->assertFalse($this->model->exists);

        $this->model->multipoint = new \Grimzy\LaravelMysqlSpatial\Types\MultiPoint([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertContains("ST_GeomFromText('MULTIPOINT((2 1),(3 2))')", $this->queries[0]);
        $this->assertTrue($this->model->exists);

        $this->model->multipoint = new \Grimzy\LaravelMysqlSpatial\Types\MultiPoint([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertContains("ST_GeomFromText('MULTIPOINT((2 1),(3 2))')", $this->queries[1]);
    }

    public function testInsertUpdateMultiLineStringHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $linestring1 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);
        $linestring2 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point3, $point4]);

        $this->assertFalse($this->model->exists);

        $this->model->multilinestring = new \Grimzy\LaravelMysqlSpatial\Types\MultiLineString([$linestring1, $linestring2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertContains("ST_GeomFromText('MULTILINESTRING((2 1,3 2),(2 3,1 2))')", $this->queries[0]);
        $this->assertTrue($this->model->exists);

        $this->model->multilinestring = new \Grimzy\LaravelMysqlSpatial\Types\MultiLineString([$linestring1, $linestring2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertContains("ST_GeomFromText('MULTILINESTRING((2 1,3 2),(2 3,1 2))')", $this->queries[1]);
    }

    public function testInsertUpdateMultiPolygonHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $linestring1 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);
        $linestring2 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point3, $point4]);
        $polygon1 = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([$linestring1, $linestring2]);

        $point5 = new Point(4, 5);
        $point6 = new Point(5, 6);
        $linestring3 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point5, $point6]);
        $point7 = new Point(6, 5);
        $point8 = new Point(5, 4);
        $linestring4 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point7, $point8]);
        $polygon2 = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([$linestring3, $linestring4]);

        $this->assertFalse($this->model->exists);

        $this->model->multipolygon = new \Grimzy\LaravelMysqlSpatial\Types\MultiPolygon([$polygon1, $polygon2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertContains("ST_GeomFromText('MULTIPOLYGON(((2 1,3 2),(2 3,1 2)),((5 4,6 5),(5 6,4 5)))')", $this->queries[0]);
        $this->assertTrue($this->model->exists);

        $this->model->multipolygon = new \Grimzy\LaravelMysqlSpatial\Types\MultiPolygon([$polygon1, $polygon2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertContains("ST_GeomFromText('MULTIPOLYGON(((2 1,3 2),(2 3,1 2)),((5 4,6 5),(5 6,4 5)))')", $this->queries[1]);
    }

    public function testInsertUpdateGeometryCollectionHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $point3 = new Point(3, 3);
        $linestring1 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point2, $point3]);

        $this->assertFalse($this->model->exists);

        $this->model->geometrycollection = new \Grimzy\LaravelMysqlSpatial\Types\GeometryCollection([$point1, $linestring1]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertContains("ST_GeomFromText('GEOMETRYCOLLECTION(POINT(2 1),LINESTRING(3 2,3 3))')", $this->queries[0]);
        $this->assertTrue($this->model->exists);

        $this->model->geometrycollection = new \Grimzy\LaravelMysqlSpatial\Types\GeometryCollection([$point1, $linestring1]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertContains("ST_GeomFromText('GEOMETRYCOLLECTION(POINT(2 1),LINESTRING(3 2,3 3))')", $this->queries[1]);
    }

    public function testScopeDistance()
    {
        $point = new Point(1, 2);
        $query = TestModel::Distance(10, $point, 'point');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $this->assertContains("st_distance(`point`, ST_GeomFromText('POINT(2 1)')) <= 10", $q->wheres[0]['sql']);
    }

    public function testScopeDistanceExcludingSelf()
    {
        $point = new Point(1, 2);
        $query = TestModel::Distance(10, $point, 'point', true);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $this->assertContains("st_distance(`point`, ST_GeomFromText('POINT(2 1)')) <= 10", $q->wheres[0]['sql']);
        $this->assertContains("st_distance(`point`, ST_GeomFromText('POINT(2 1)')) != 0", $q->wheres[1]['sql']);
    }

    public function testScopeDistanceSphere()
    {
        $point = new Point(1, 2);
        $query = TestModel::DistanceSphere(10, $point, 'point');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $this->assertContains("st_distance_sphere(`point`, ST_GeomFromText('POINT(2 1)')) <= 10", $q->wheres[0]['sql']);
    }

    public function testScopeDistanceSphereExcludingSelf()
    {
        $point = new Point(1, 2);
        $query = TestModel::DistanceSphere(10, $point, 'point', true);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $this->assertContains("st_distance_sphere(`point`, ST_GeomFromText('POINT(2 1)')) <= 10", $q->wheres[0]['sql']);
        $this->assertContains("st_distance_sphere(`point`, ST_GeomFromText('POINT(2 1)')) != 0", $q->wheres[1]['sql']);
    }

    public function testScopeDistanceValue()
    {
        $point = new Point(1, 2);
        $query = TestModel::DistanceValue($point, 'point');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $this->assertContains("*", $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertContains("st_distance(`point`, ST_GeomFromText('POINT(2 1)')) as distance", $q->columns[1]->getValue());
    }

    public function testScopeDistanceValueWithSelect()
    {
        $point = new Point(1, 2);
        $query = TestModel::select('some_column')->distanceValue($point, 'point');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $this->assertContains("some_column", $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertContains("st_distance(`point`, ST_GeomFromText('POINT(2 1)')) as distance", $q->columns[1]->getValue());
    }

    public function testScopeDistanceSphereValue()
    {
        $point = new Point(1, 2);
        $query = TestModel::DistanceSphereValue($point, 'point');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $this->assertContains("*", $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertContains("st_distance_sphere(`point`, ST_GeomFromText('POINT(2 1)')) as distance", $q->columns[1]->getValue());
    }

    public function testScopeDistanceSphereValueWithSelect()
    {
        $point = new Point(1, 2);
        $query = TestModel::select('some_column')->distanceSphereValue($point, 'point');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $this->assertContains("some_column", $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertContains("st_distance_sphere(`point`, ST_GeomFromText('POINT(2 1)')) as distance", $q->columns[1]->getValue());
    }

    public function testScopeBounding()
    {
        $point1 = new Point(1, 1);
        $point2 = new Point(1, 2);
        $linestring1 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $point3 = new Point(1, 2);
        $point4 = new Point(2, 2);
        $linestring2 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point3, $point4]);
        $point5 = new Point(2, 2);
        $point6 = new Point(1, 1);
        $linestring3 = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point5, $point6]);

        $polygon = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([$linestring1, $linestring2, $linestring3]);
        $query = TestModel::Bounding($polygon, 'point');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $this->assertContains("st_intersects(ST_GeomFromText('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))'), `point`)", $q->wheres[0]['sql']);
    }
}

class TestModel extends Model
{
    use \Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

    protected $spatialFields = ['point'];   // TODO: only required when fetching, not saving

    public $timestamps = false;

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

    public function prepare($statement, $driver_options = [])
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
