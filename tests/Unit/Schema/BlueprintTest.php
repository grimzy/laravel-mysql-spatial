<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Unit\Schema;

use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Grimzy\LaravelMysqlSpatial\Tests\Unit\BaseTestCase as UnitBaseTestCase;
use Illuminate\Database\Schema\ColumnDefinition;
use Mockery;

class BlueprintTest extends UnitBaseTestCase
{
    protected \Grimzy\LaravelMysqlSpatial\Schema\Blueprint $blueprint;

    public function setUp(): void
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
            ->with('geometry', 'col', ['srid' => null])
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
            ->with('linestring', 'col', ['srid' => null])
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
            ->with('polygon', 'col', ['srid' => null])
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
            ->with('multipoint', 'col', ['srid' => null])
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
            ->with('multilinestring', 'col', ['srid' => null])
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
            ->with('multipolygon', 'col', ['srid' => null])
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
            ->with('geometrycollection', 'col', ['srid' => null])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->geometrycollection('col');

        $this->assertSame($expectedCol, $result);
    }

    public function testGeometryWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'geometry',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometry', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->geometry('col', 4326);

        $this->assertSame($expectedCol, $result);
    }

    public function testPointWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'point',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('point', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->point('col', 4326);

        $this->assertSame($expectedCol, $result);
    }

    public function testLinestringWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'linestring',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('linestring', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->linestring('col', 4326);

        $this->assertSame($expectedCol, $result);
    }

    public function testPolygonWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'polygon',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('polygon', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->polygon('col', 4326);

        $this->assertSame($expectedCol, $result);
    }

    public function testMultiPointWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'multipoint',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipoint', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->multipoint('col', 4326);

        $this->assertSame($expectedCol, $result);
    }

    public function testMultiLineStringWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'multilinestring',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multilinestring', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->multilinestring('col', 4326);

        $this->assertSame($expectedCol, $result);
    }

    public function testMultiPolygonWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'multipolygon',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('multipolygon', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->multipolygon('col', 4326);

        $this->assertSame($expectedCol, $result);
    }

    public function testGeometryCollectionWithSrid()
    {
        $expectedCol = new ColumnDefinition([
            'type' => 'geometrycollection',
            'name' => 'col',
            'srid' => 4326,
        ]);

        $this->blueprint
            ->shouldReceive('addColumn')
            ->with('geometrycollection', 'col', ['srid' => 4326])
            ->once()
            ->andReturn($expectedCol);

        $result = $this->blueprint->geometrycollection('col', 4326);

        $this->assertSame($expectedCol, $result);
    }
}
