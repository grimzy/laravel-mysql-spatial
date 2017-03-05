<?php namespace Eloquent;

use BaseTestCase;
use Grimzy\LaravelSpatial\Eloquent\Builder;
use Grimzy\LaravelSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelSpatial\MysqlConnection;
use Grimzy\LaravelSpatial\Types\LineString;
use Grimzy\LaravelSpatial\Types\Point;
use Grimzy\LaravelSpatial\Types\Polygon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Mockery;

class BuilderTest extends BaseTestCase
{
    protected $builder;

    /**
     * @var \Mockery\MockInterface $queryBuilder
     */
    protected $queryBuilder;

    protected function setUp()
    {
        $connection = Mockery::mock(MysqlConnection::class)->makePartial();
        $this->queryBuilder = Mockery::mock(QueryBuilder::class, [$connection])->makePartial();

        $this->queryBuilder
            ->shouldReceive('from')
            ->andReturn($this->queryBuilder);

        $this->queryBuilder
            ->shouldReceive('take')
            ->with(1)
            ->andReturn($this->queryBuilder);

        $this->queryBuilder
            ->shouldReceive('get')
            ->andReturn([]);

        $this->builder = new Builder($this->queryBuilder);
        $this->builder->setModel(new TestBuilderModel());
    }

    public function testUpdate()
    {
        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeomFromText('POINT(2 1)')")
            ->andReturn(new Expression("ST_GeomFromText('POINT(2 1)')"));

        $this->queryBuilder
            ->shouldReceive('update');

        $builder = Mockery::mock(Builder::class, [$this->queryBuilder])->makePartial();
        $builder->shouldAllowMockingProtectedMethods();
        $builder
            ->shouldReceive('addUpdatedAtColumn')
            ->andReturn(['point' => new Point(1, 2)]);

        $builder->update(['point' => new Point(1, 3)]);
    }

    public function testUpdateLinestring()
    {
        $this->queryBuilder
            ->shouldReceive('raw')
            ->with("ST_GeogFromText('LINESTRING(0 0, 1 1, 2 2)')")
            ->andReturn(new Expression("ST_GeogFromText('LINESTRING(0 0, 1 1, 2 2)')"));

        $this->queryBuilder
            ->shouldReceive('update')
            ->andReturn(1);

        $linestring = new LineString([new Point(0, 0), new Point(1, 1), new Point(2, 2)]);

        $builder = Mockery::mock(Builder::class, [$this->queryBuilder])->makePartial();
        $builder->shouldAllowMockingProtectedMethods();
        $builder
            ->shouldReceive('addUpdatedAtColumn')
            ->andReturn(['linestring' => $linestring]);

        $builder
            ->shouldReceive('asWKT')->with($linestring)->once();

        $builder->update(['linestring' => $linestring]);
    }
}

class TestBuilderModel extends Model
{
    use SpatialTrait;

    protected $spatialFields = [
        'point' => Point::class,
        'linestring' => LineString::class,
        'polygon' => Polygon::class
    ];
}