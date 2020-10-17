<?php

use Grimzy\LaravelMysqlSpatial\Types\Point;

class PointTest extends BaseTestCase
{
    public function testFromWKT()
    {
        $point = Point::fromWKT('POINT(1 2)');

        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(2, $point->getLat());
        $this->assertEquals(1, $point->getLng());
    }

    public function testToWKT()
    {
        $point = new Point(1, 2);

        $this->assertEquals('POINT(2 1)', $point->toWKT());
    }

    public function testGettersAndSetters()
    {
        $point = new Point(1, 2);
        $this->assertSame(1.0, $point->getLat());
        $this->assertSame(2.0, $point->getLng());

        $point->setLat('3');
        $point->setLng('4');

        $this->assertSame(3.0, $point->getLat());
        $this->assertSame(4.0, $point->getLng());
    }

    public function testPair()
    {
        $point = Point::fromPair('1.5 2');

        $this->assertSame(1.5, $point->getLng());
        $this->assertSame(2.0, $point->getLat());

        $this->assertSame('1.5 2', $point->toPair());
    }

    public function testToString()
    {
        $point = Point::fromString('1.3 2');

        $this->assertSame(1.3, $point->getLng());
        $this->assertSame(2.0, $point->getLat());

        $this->assertEquals('1.3 2', (string) $point);
    }

    public function testFromJson()
    {
        $point = Point::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(1.2, $point->getLat());
        $this->assertEquals(3.4, $point->getLng());
    }

    public function testInvalidGeoJsonException()
    {
        $this->assertException(
            \Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException::class,
            'Expected GeoJson\Geometry\Point, got GeoJson\Geometry\LineString'
        );
        Point::fromJson('{"type": "LineString","coordinates":[[1,1],[2,2]]}');
    }

    public function testJsonSerialize()
    {
        $point = new Point(1.2, 3.4);

        $this->assertInstanceOf(\GeoJson\Geometry\Point::class, $point->jsonSerialize());
        $this->assertSame('{"type":"Point","coordinates":[3.4,1.2]}', json_encode($point));
    }
}
