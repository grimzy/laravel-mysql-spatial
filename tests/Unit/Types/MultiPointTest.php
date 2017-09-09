<?php

use Grimzy\LaravelMysqlSpatial\Types\MultiPoint;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class MultiPointTest extends BaseTestCase
{
    public function testFromWKT()
    {
        $multipoint = MultiPoint::fromWKT('MULTIPOINT((0 0),(1 0),(1 1))');
        $this->assertInstanceOf(MultiPoint::class, $multipoint);

        $this->assertEquals(3, $multipoint->count());
    }

    public function testToWKT()
    {
        $collection = [new Point(0, 0), new Point(0, 1), new Point(1, 1)];

        $multipoint = new MultiPoint($collection);

        $this->assertEquals('MULTIPOINT((0 0),(1 0),(1 1))', $multipoint->toWKT());
    }

    public function testGetPoints()
    {
        $multipoint = MultiPoint::fromWKT('MULTIPOINT((0 0),(1 0),(1 1))');

        $this->assertInstanceOf(Point::class, $multipoint->getPoints()[0]);
    }

    public function testToArray()
    {
        $multipoint = MultiPoint::fromWKT('MULTIPOINT((0 0),(1 0),(1 1))');

        $this->assertInstanceOf(Point::class, $multipoint->toArray()[0]);
    }

    public function testIteratorAggregate() {
        $multipoint = MultiPoint::fromWKT('MULTIPOINT((0 0),(1 0),(1 1))');

        foreach($multipoint as $value) {
            $this->assertInstanceOf(Point::class, $value);
        }
    }

    public function testArrayAccess() {

    }

    public function testJsonSerialize()
    {
        $collection = [new Point(0, 0), new Point(0, 1), new Point(1, 1)];

        $multipoint = new MultiPoint($collection);

        $this->assertInstanceOf(\GeoJson\Geometry\MultiPoint::class, $multipoint->jsonSerialize());
        $this->assertSame('{"type":"MultiPoint","coordinates":[[0,0],[1,0],[1,1]]}', json_encode($multipoint));
    }

    public function testInvalidArgumentExceptionAtLeastOneEntry() {
        $this->expectException(InvalidArgumentException::class);
        $multipoint = new MultiPoint([]);
    }

    public function testInvalidArgumentExceptionNotArrayOfLineString() {
        $this->expectException(InvalidArgumentException::class);
        $multipoint = new MultiPoint([
            new Point(0, 0),
            1
        ]);
    }
}
