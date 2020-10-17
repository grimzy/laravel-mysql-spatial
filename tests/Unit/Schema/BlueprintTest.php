<?php

namespace Schema;

use BaseTestCase;
use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
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
        $expectedCol = new ColumnDefinition([
            'type' => 'geometry',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometry', 'col')
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->geometry('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testPoint()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'point',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('point', 'col', ['srid' => null])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->point('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testLinestring()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'linestring',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('linestring', 'col')
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->linestring('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testPolygon()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'polygon',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('polygon', 'col')
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->polygon('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testMultiPoint()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'multipoint',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipoint', 'col')
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->multipoint('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testMultiLineString()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'multilinestring',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multilinestring', 'col')
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->multilinestring('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testMultiPolygon()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'multipolygon',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipolygon', 'col')
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->multipolygon('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testGeometryCollection()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'geometrycollection',
            'name' => 'col',
            'srid' => null,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometrycollection', 'col')
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->geometrycollection('col');

        $this->assertSame($expectedCol, $result);
    }
}
