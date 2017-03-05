<?php

use Grimzy\LaravelSpatial\Types\LineString;
use Grimzy\LaravelSpatial\Types\Point;

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

        $this->assertEquals('0 0,1 1,2 2', (string)$linestring);
    }

    public function testJsonSerialize()
    {
        $lineString = new LineString($this->points);

        $this->assertInstanceOf(\GeoJson\Geometry\LineString::class, $lineString->jsonSerialize());
        $this->assertSame('{"type":"LineString","coordinates":[[0,0],[1,1],[2,2]]}', json_encode($lineString));
    }
}
