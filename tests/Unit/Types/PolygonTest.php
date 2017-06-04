<?php

use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;

class PolygonTest extends BaseTestCase
{
    private $polygon;

    protected function setUp()
    {
        $collection = new LineString(
            [
                new Point(0, 0),
                new Point(0, 1),
                new Point(1, 1),
                new Point(1, 0),
                new Point(0, 0)
            ]
        );

        $this->polygon = new Polygon([$collection]);
    }


    public function testFromWKT()
    {
        $polygon = Polygon::fromWKT('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))');
        $this->assertInstanceOf(Polygon::class, $polygon);

        $this->assertEquals(2, $polygon->count());
    }

    public function testToWKT()
    {
        $this->assertEquals('POLYGON((0 0,1 0,1 1,0 1,0 0))', $this->polygon->toWKT());
    }

    public function testJsonSerialize()
    {
        $this->assertInstanceOf(\GeoJson\Geometry\Polygon::class, $this->polygon->jsonSerialize());
        $this->assertSame(
            '{"type":"Polygon","coordinates":[[[0,0],[1,0],[1,1],[0,1],[0,0]]]}',
            json_encode($this->polygon)
        );

    }
}
