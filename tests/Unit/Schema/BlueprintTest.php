<?php

namespace Schema;

use BaseTestCase;
use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Mockery;

class BlueprintTest extends BaseTestCase
{
    /**
     * @var \Grimzy\LaravelMysqlSpatial\Schema\Blueprint
     */
    protected $blueprint;

    public function setUp()
    {
        parent::setUp();

        $this->blueprint = Mockery::mock(Blueprint::class)
            ->makePartial()->shouldAllowMockingProtectedMethods();
    }

    public function testGeometry()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometry', 'col', ['srid' => null])
            ->once();

        $this->blueprint->geometry('col');
    }

    public function testPoint()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('point', 'col', ['srid' => null])
            ->once();

        $this->blueprint->point('col');
    }

    public function testLinestring()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('linestring', 'col', ['srid' => null])
            ->once();

        $this->blueprint->linestring('col');
    }

    public function testPolygon()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('polygon', 'col', ['srid' => null])
            ->once();

        $this->blueprint->polygon('col');
    }

    public function testMultiPoint()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipoint', 'col', ['srid' => null])
            ->once();

        $this->blueprint->multipoint('col');
    }

    public function testMultiLineString()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multilinestring', 'col', ['srid' => null])
            ->once();

        $this->blueprint->multilinestring('col');
    }

    public function testMultiPolygon()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipolygon', 'col', ['srid' => null])
            ->once();

        $this->blueprint->multipolygon('col');
    }

    public function testGeometryCollection()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometrycollection', 'col', ['srid' => null])
            ->once();

        $this->blueprint->geometrycollection('col');
    }

    public function testGeometryWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometry', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->geometry('col', 4326);
    }

    public function testPointWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('point', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->point('col', 4326);
    }

    public function testLinestringWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('linestring', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->linestring('col', 4326);
    }

    public function testPolygonWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('polygon', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->polygon('col', 4326);
    }

    public function testMultiPointWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipoint', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->multipoint('col', 4326);
    }

    public function testMultiLineStringWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multilinestring', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->multilinestring('col', 4326);
    }

    public function testMultiPolygonWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipolygon', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->multipolygon('col', 4326);
    }

    public function testGeometryCollectionWithSrid()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometrycollection', 'col', ['srid' => 4326])
            ->once();

        $this->blueprint->geometrycollection('col', 4326);
    }
}
