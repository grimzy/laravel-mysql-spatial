<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Integration;

use Grimzy\LaravelMysqlSpatial\Tests\Integration\Models\WithSridModel;
use Grimzy\LaravelMysqlSpatial\Types\GeometryCollection;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiPoint;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;

class SridSpatialTest extends IntegrationBaseCase
{
    protected $migrations = [
        CreateTables::class,
        UpdateTables::class,
    ];

    public function testInsertPointWithSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2, 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertLineStringWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);
        $geo->line = new LineString([new Point(1, 1), new Point(2, 2)], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertPolygonWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);
        $geo->shape = Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))', 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertMultiPointWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);
        $geo->multi_locations = new MultiPoint([new Point(1, 1), new Point(2, 2)], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertMultiPolygonWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);

        $geo->multi_shapes = new MultiPolygon([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
        ], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testInsertGeometryCollectionWithSrid()
    {
        $geo = new WithSridModel();

        $geo->location = new Point(1, 2, 3857);

        $geo->multi_geometries = new GeometryCollection([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
            new Point(0, 0),
        ], 3857);
        $geo->save();
        $this->assertDatabaseHas('with_srid', ['id' => $geo->id]);
    }

    public function testUpdateWithSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2, 3857);
        $geo->save();

        $to_update = WithSridModel::all()->first();
        $to_update->location = new Point(2, 3, 3857);
        $to_update->save();

        $this->assertDatabaseHas('with_srid', ['id' => $to_update->id]);

        $all = WithSridModel::all();
        $this->assertCount(1, $all);

        $updated = $all->first();
        $this->assertInstanceOf(Point::class, $updated->location);
        $this->assertEquals(2, $updated->location->getLat());
        $this->assertEquals(3, $updated->location->getLng());
    }

    public function testInsertPointWithWrongSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2);

        $this->assertException(
            \Illuminate\Database\QueryException::class,
            "SQLSTATE[HY000]: General error: 3643 The SRID of the geometry does not match the SRID of the column 'location'. The SRID of the geometry is 0, but the SRID of the column is 3857. Consider changing the SRID of the geometry or the SRID property of the column. (Connection: mysql, SQL: insert into `with_srid` (`location`) values (ST_GeomFromText(POINT(2 1), 0, 'axis-order=long-lat')))"
        );
        $geo->save();
    }

    public function testGeometryInsertedHasRightSrid()
    {
        $geo = new WithSridModel();
        $geo->location = new Point(1, 2, 3857);
        $geo->save();

        $srid = \DB::selectOne('select ST_SRID(location) as srid from with_srid');
        $this->assertEquals(3857, $srid->srid);

        $result = WithSridModel::first();

        $this->assertEquals($geo->location->getSrid(), $result->location->getSrid());
        $a = 1;
    }
}
