<?php

namespace Schema;

use BaseTestCase;
use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Mockery;

class BlueprintTest extends BaseTestCase
{
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
            ->with('geometry', 'col')
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
            ->with('linestring', 'col')
            ->once();

        $this->blueprint->linestring('col');
    }

    public function testPolygon()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('polygon', 'col')
            ->once();

        $this->blueprint->polygon('col');
    }

    public function testMultiPoint()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipoint', 'col')
            ->once();

        $this->blueprint->multipoint('col');
    }

    public function testMultiLineString()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multilinestring', 'col')
            ->once();

        $this->blueprint->multilinestring('col');
    }

    public function testMulltiPolygon()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipolygon', 'col')
            ->once();

        $this->blueprint->multipolygon('col');
    }

    public function testGeometryCollection()
    {
        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometrycollection', 'col')
            ->once();

        $this->blueprint->geometrycollection('col');
    }
}
