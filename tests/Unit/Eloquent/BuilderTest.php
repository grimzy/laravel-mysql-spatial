<?php

namespace Eloquent;

use BaseTestCase;
use Grimzy\LaravelMysqlSpatial\Eloquent\Builder;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\MysqlConnection;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Mockery;

class BuilderTest extends BaseTestCase
{
    protected $builder;
    protected $queryBuilder;

    protected function setUp()
    {
        $connection = Mockery::mock(MysqlConnection::class)->makePartial();
        $grammar = Mockery::mock(MySqlGrammar::class)->makePartial();
        $this->queryBuilder = Mockery::mock(QueryBuilder::class, [$connection, $grammar]);

        $this->queryBuilder
            ->shouldReceive('from')
            ->once()
            ->andReturn($this->queryBuilder);

        $this->builder = new Builder($this->queryBuilder);
        $this->builder->setModel(new TestBuilderModel());
    }

    public function testUpdatePoint()
    {
        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeomFromText('POINT(2 1)')")
            ->once();

        $this->queryBuilder
            ->shouldReceive('update')
            ->once();

        $this->builder->update(['point' => new Point(1, 2)]);
    }

    public function testUpdateLinestring()
    {
        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeomFromText('LINESTRING(0 0,1 1,2 2)')")
            ->once();

        $this->queryBuilder
            ->shouldReceive('update')
            ->once();

        $linestring = new LineString([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

        $this->builder->update(['linestring' => $linestring]);
    }

    public function testUpdatePolygon()
    {
        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeomFromText('POLYGON((0 0,1 0),(1 0,1 1),(1 1,0 0))')")
            ->once();

        $this->queryBuilder
            ->shouldReceive('update')
            ->once();

        $linestrings[] = new LineString([new Point(0, 0), new Point(0, 1)]);
        $linestrings[] = new LineString([new Point(0, 1), new Point(1, 1)]);
        $linestrings[] = new LineString([new Point(1, 1), new Point(0, 0)]);
        $polygon = new Polygon($linestrings);

        $this->builder->update(['polygon' => $polygon]);
    }
}

class TestBuilderModel extends Model
{
    use SpatialTrait;

    public $timestamps = false;
    protected $spatialFields = ['point', 'linestring', 'polygon'];
}
