<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Integration;

use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Exceptions\SpatialFieldsNotDefinedException;
use Grimzy\LaravelMysqlSpatial\Tests\Integration\Eloquent\TestModel;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class SpatialTraitTest extends IntegrationBaseCase
{
    use MockeryPHPUnitIntegration;

    protected TestModel $model;

    protected array $queries = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->model = new TestModel();
        DB::listen(function (QueryExecuted $query) {
            $this->queries[] = $query->sql;
        });
    }

    public function tearDown(): void
    {
        $this->queries = [];
    }

    public function testInsertUpdatePointHasCorrectSql()
    {
        $this->assertFalse($this->model->exists);

        $this->model->point = new Point(1, 2);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`point`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->point = new Point(1, 2);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertStringContainsString('update `test_models` set `point` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateLineStringHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);

        $this->assertFalse($this->model->exists);

        $this->model->linestring = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`linestring`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->linestring = new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertStringContainsString('update `test_models` set `linestring` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdatePolygonHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);

        $polygon = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([
            new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2, $point3, $point4, $point1]),
        ]);

        $this->assertFalse($this->model->exists);

        $this->model->polygon = $polygon;
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`polygon`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $polygon = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([
            new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2, $point3, $point4, $point1]),
        ]);

        $this->model->polygon = $polygon;
        $this->model->save();

        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertStringContainsString('update `test_models` set `polygon` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateMultiPointHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);

        $this->assertFalse($this->model->exists);

        $this->model->multipoint = new \Grimzy\LaravelMysqlSpatial\Types\MultiPoint([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`multipoint`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->multipoint = new \Grimzy\LaravelMysqlSpatial\Types\MultiPoint([$point1, $point2]);
        $this->model->save();

        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertStringContainsString('update `test_models` set `multipoint` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->queries[1]);
        // TODO: assert bindings in query
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
        $this->assertStringContainsString('insert into `test_models` (`multilinestring`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->multilinestring = new \Grimzy\LaravelMysqlSpatial\Types\MultiLineString([$linestring1, $linestring2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertStringContainsString('update `test_models` set `multilinestring` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->queries[1]);
        // TODO: assert bindings in query
    }

    public function testInsertUpdateMultiPolygonHasCorrectSql()
    {
        $point1 = new Point(1, 2);
        $point2 = new Point(2, 3);
        $point3 = new Point(3, 2);
        $point4 = new Point(2, 1);
        $polygon1 = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([
            new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point1, $point2, $point3, $point1]),
        ]);

        $point5 = new Point(4, 5);
        $point6 = new Point(5, 6);
        $point7 = new Point(6, 5);
        $point8 = new Point(5, 4);
        $polygon2 = new \Grimzy\LaravelMysqlSpatial\Types\Polygon([
            new \Grimzy\LaravelMysqlSpatial\Types\LineString([$point5, $point6, $point7, $point5]),
        ]);

        $this->assertFalse($this->model->exists);

        $this->model->multipolygon = new \Grimzy\LaravelMysqlSpatial\Types\MultiPolygon([$polygon1, $polygon2]);
        $this->model->save();

        $this->assertStringStartsWith('insert', $this->queries[0]);
        $this->assertStringContainsString('insert into `test_models` (`multipolygon`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->multipolygon = new \Grimzy\LaravelMysqlSpatial\Types\MultiPolygon([$polygon1, $polygon2]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertStringContainsString('update `test_models` set `multipolygon` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->queries[1]);
        // TODO: assert bindings in query
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
        $this->assertStringContainsString('insert into `test_models` (`geometrycollection`) values (ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $this->queries[0]);
        // TODO: assert bindings in query
        $this->assertTrue($this->model->exists);

        $this->model->geometrycollection = new \Grimzy\LaravelMysqlSpatial\Types\GeometryCollection([$point1, $linestring1]);
        $this->model->save();
        $this->assertStringStartsWith('update', $this->queries[1]);
        $this->assertStringContainsString('update `test_models` set `geometrycollection` = ST_GeomFromText(?, ?, \'axis-order=long-lat\') where `id` = ?', $this->queries[1]);
        // TODO: assert bindings in query
    }

    public function testSettingRawAttributes()
    {
        $attributes['point'] = "\0\0\0\0".'0101000000000000000000f03f0000000000000040';

        $this->model->setRawAttributes($attributes);
        $this->assertInstanceOf(Point::class, ($this->model->point));
    }

    public function testSpatialFieldsNotDefinedException()
    {
        $model = new class extends Model
        {
            use SpatialTrait;
        };
        $this->assertException(
            SpatialFieldsNotDefinedException::class,
            'TestNoSpatialModel'
        );
        $this->assertException(
            SpatialFieldsNotDefinedException::class,
            ' has to define $spatialFields'
        );
        $model->getSpatialFields();
    }

    public function testScopeDistance()
    {
        $point = new Point(1, 2);
        $query = TestModel::distance('point', $point, 10);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ?', $q->wheres[0]['sql']);
        $this->assertEquals('POINT(2 1)', $bindings[0]);
        $this->assertEquals(10, $bindings[2]);
    }

    public function testScopeDistanceExcludingSelf()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceExcludingSelf('point', $point, 10);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ?', $q->wheres[0]['sql']);
        $this->assertEquals('st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) != 0', $q->wheres[1]['sql']);
        $this->assertEquals('POINT(2 1)', $bindings[0]);
        $this->assertEquals(10, $bindings[2]);
        $this->assertEquals('POINT(2 1)', $bindings[3]);
    }

    public function testScopeDistanceSphere()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceSphere('point', $point, 10);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ?', $q->wheres[0]['sql']);
        $this->assertEquals('POINT(2 1)', $bindings[0]);
        $this->assertEquals(10, $bindings[2]);
    }

    public function testScopeDistanceSphereExcludingSelf()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceSphereExcludingSelf('point', $point, 10);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) <= ?', $q->wheres[0]['sql']);
        $this->assertEquals('st_distance_sphere(point, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) != 0', $q->wheres[1]['sql']);
        $this->assertEquals('POINT(2 1)', $bindings[0]);
        $this->assertEquals(10, $bindings[2]);
        $this->assertEquals('POINT(2 1)', $bindings[3]);
    }

    public function testScopeDistanceValue()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceValue('point', $point);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $bindings = $q->getRawBindings()['select'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('*', $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertEquals('st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance', $q->columns[1]->getValue(DB::connection()->getQueryGrammar()));
        $this->assertEquals('POINT(2 1)', $bindings[0]);
    }

    public function testScopeDistanceValueWithSelect()
    {
        $point = new Point(1, 2);
        $query = TestModel::select('some_column')->distanceValue('point', $point);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $bindings = $q->getRawBindings()['select'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('some_column', $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertEquals('st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance', $q->columns[1]->getValue(DB::connection()->getQueryGrammar()));
        $this->assertEquals('POINT(2 1)', $bindings[0]);
    }

    public function testScopeDistanceSphereValue()
    {
        $point = new Point(1, 2);
        $query = TestModel::distanceSphereValue('point', $point);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $bindings = $q->getRawBindings()['select'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('*', $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertEquals('st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance', $q->columns[1]->getValue(DB::connection()->getQueryGrammar()));
        $this->assertEquals('POINT(2 1)', $bindings[0]);
    }

    public function testScopeDistanceSphereValueWithSelect()
    {
        $point = new Point(1, 2);
        $query = TestModel::select('some_column')->distanceSphereValue('point', $point);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->columns);
        $bindings = $q->getRawBindings()['select'];
        $this->assertNotEmpty($bindings);
        $this->assertEquals('some_column', $q->columns[0]);
        $this->assertInstanceOf(\Illuminate\Database\Query\Expression::class, $q->columns[1]);
        $this->assertEquals('st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) as distance', $q->columns[1]->getValue(DB::connection()->getQueryGrammar()));
        $this->assertEquals('POINT(2 1)', $bindings[0]);
    }

    private function buildTestPolygon()
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

        return new \Grimzy\LaravelMysqlSpatial\Types\Polygon([$linestring1, $linestring2, $linestring3]);
    }

    public function testScopeComparison()
    {
        $query = TestModel::comparison('point', $this->buildTestPolygon(), 'within');

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_within(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeWithin()
    {
        $query = TestModel::within('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_within(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeCrosses()
    {
        $query = TestModel::crosses('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_crosses(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeContains()
    {
        $query = TestModel::contains('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_contains(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeDisjoint()
    {
        $query = TestModel::disjoint('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_disjoint(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeEquals()
    {
        $query = TestModel::equals('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_equals(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeIntersects()
    {
        $query = TestModel::intersects('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_intersects(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeOverlaps()
    {
        $query = TestModel::overlaps('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_overlaps(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeDoesTouch()
    {
        $query = TestModel::doesTouch('point', $this->buildTestPolygon());

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->wheres);
        $bindings = $q->getRawBindings()['where'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_touches(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\'))', $q->wheres[0]['sql']);
        $this->assertEquals('POLYGON((1 1,2 1),(2 1,2 2),(2 2,1 1))', $bindings[0]);
    }

    public function testScopeOrderBySpatialThrowsExceptionWhenFunctionNotRegistered()
    {
        $point = new Point(1, 2);
        $this->assertException(
            \Grimzy\LaravelMysqlSpatial\Exceptions\UnknownSpatialFunctionException::class,
            'does-not-exist'
        );
        TestModel::orderBySpatial('point', $point, 'does-not-exist');
    }

    public function testScopeOrderByDistance()
    {
        $point = new Point(1, 2);
        $query = TestModel::orderByDistance('point', $point);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->orders);
        $bindings = $q->getRawBindings()['order'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_distance(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) asc', $q->orders[0]['sql']);
        $this->assertEquals('POINT(2 1)', $bindings[0]);
    }

    public function testScopeOrderByDistanceSphere()
    {
        $point = new Point(1, 2);
        $query = TestModel::orderByDistanceSphere('point', $point);

        $this->assertInstanceOf(\Grimzy\LaravelMysqlSpatial\Eloquent\Builder::class, $query);
        $q = $query->getQuery();
        $this->assertNotEmpty($q->orders);
        $bindings = $q->getRawBindings()['order'];
        $this->assertNotEmpty($bindings);
        $this->assertStringContainsString('st_distance_sphere(`point`, ST_GeomFromText(?, ?, \'axis-order=long-lat\')) asc', $q->orders[0]['sql']);
        $this->assertEquals('POINT(2 1)', $bindings[0]);
    }
}
