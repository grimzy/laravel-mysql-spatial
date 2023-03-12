<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Unit\Types;

use Grimzy\LaravelMysqlSpatial\Tests\Unit\BaseTestCase;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class PointTest extends BaseTestCase
{
    public function testFromWKT()
    {
        $point = Point::fromWKT('POINT(1 2)', 4326);

        $this->assertInstanceOf(Point::class, $point);
        $this->assertSame(2.0, $point->getLat());
        $this->assertSame(1.0, $point->getLng());
        $this->assertSame(4326, $point->getSrid());
    }

    public function testToWKT()
    {
        $point = new Point(1, 2, 4326);

        $this->assertSame('POINT(2 1)', $point->toWKT());
    }

    public function testGettersAndSetters()
    {
        $point = new Point(1, 2);
        $this->assertSame(1.0, $point->getLat());
        $this->assertSame(2.0, $point->getLng());
        $this->assertSame(0, $point->getSrid());

        $point->setLat('3');
        $point->setLng('4');
        $point->setSrid(100);

        $this->assertSame(3.0, $point->getLat());
        $this->assertSame(4.0, $point->getLng());
        $this->assertSame(100, $point->getSrid());
    }

    public function testPair()
    {
        $point = Point::fromPair('1.5 2', 4326);

        $this->assertSame(1.5, $point->getLng());
        $this->assertSame(2.0, $point->getLat());
        $this->assertSame(4326, $point->getSrid());

        $this->assertSame('1.5 2', $point->toPair());
    }

    public function testToString()
    {
        $point = Point::fromString('1.3 2', 4326);

        $this->assertSame(1.3, $point->getLng());
        $this->assertSame(2.0, $point->getLat());
        $this->assertSame(4326, $point->getSrid());

        $this->assertEquals('1.3 2', (string) $point);
    }

    public function testFromJson()
    {
        $point = Point::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
        $this->assertInstanceOf(Point::class, $point);
        $this->assertSame(1.2, $point->getLat());
        $this->assertSame(3.4, $point->getLng());
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
