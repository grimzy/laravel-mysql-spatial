<?php

use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class LineStringTest extends BaseTestCase
{
    private $points;

    protected function setUp()
    {
        $this->points = [new Point(0, 0), new Point(1, 1), new Point(2, 2)];
    }

    public function testToWKT()
    {
        $linestring = new LineString($this->points);

        $this->assertEquals('LINESTRING(0 0,1 1,2 2)', $linestring->toWKT());
    }

    public function testFromWKT()
    {
        $linestring = LineString::fromWKT('LINESTRING(0 0, 1 1, 2 2)');
        $this->assertInstanceOf(LineString::class, $linestring);

        $this->assertEquals(3, $linestring->count());
    }

    public function testToString()
    {
        $linestring = new LineString($this->points);

        $this->assertEquals('0 0,1 1,2 2', (string) $linestring);
    }

    public function testFromJson()
    {
        $lineString = LineString::fromJson('{"type": "LineString","coordinates":[[1,1],[2,2]]}');
        $this->assertInstanceOf(LineString::class, $lineString);
        $lineStringPoints = $lineString->getGeometries();
        $this->assertEquals(new Point(1, 1), $lineStringPoints[0]);
        $this->assertEquals(new Point(2, 2), $lineStringPoints[1]);
    }

    public function testInvalidGeoJsonException()
    {
        $this->assertException(
            \Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException::class,
            sprintf('Expected %s, got %s', \GeoJson\Geometry\LineString::class, GeoJson\Geometry\Point::class)
        );
        LineString::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
    }

    public function testJsonSerialize()
    {
        $lineString = new LineString($this->points);

        $this->assertInstanceOf(\GeoJson\Geometry\LineString::class, $lineString->jsonSerialize());
        $this->assertSame('{"type":"LineString","coordinates":[[0,0],[1,1],[2,2]]}', json_encode($lineString));
    }
}
