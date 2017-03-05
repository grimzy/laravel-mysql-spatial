<?php
namespace Schema;

use BaseTestCase;
use Mockery;
use Grimzy\LaravelSpatial\Schema\Blueprint;

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
            ->shouldReceive('addCommand')
            ->with('geometry', ['col', null, 2, true]);

        $this->blueprint->geometry('col');
    }

    public function testPoint()
    {
        $this->blueprint
            ->shouldReceive('addCommand')
            ->with('point', ['col', null, 2, true]);

        $this->blueprint->point('col');
    }

    public function testLinestring()
    {
        $this->blueprint
            ->shouldReceive('addCommand')
            ->with('linestring', ['col', null, 2, true]);

        $this->blueprint->linestring('col');
    }

    public function testPolygon()
    {
        $this->blueprint
            ->shouldReceive('addCommand')
            ->with('polygon', ['col', null, 2, true]);

        $this->blueprint->polygon('col');
    }

    public function testMultiPoint()
    {
        $this->blueprint
            ->shouldReceive('addCommand')
            ->with('multipoint', ['col', null, 2, true]);

        $this->blueprint->multipoint('col');
    }

    public function testMultiLineString()
    {
        $this->blueprint
            ->shouldReceive('addCommand')
            ->with('multilinestring', ['col', null, 2, true]);

        $this->blueprint->multilinestring('col');
    }

    public function testMulltiPolygon()
    {
        $this->blueprint
            ->shouldReceive('addCommand')
            ->with('multipolygon', ['col', null, 2, true]);

        $this->blueprint->multipolygon('col');
    }

    public function testGeometryCollection()
    {
        $this->blueprint
            ->shouldReceive('addCommand')
            ->with('geometrycollection', ['col', null, 2, true]);

        $this->blueprint->geometrycollection('col');
    }


}
