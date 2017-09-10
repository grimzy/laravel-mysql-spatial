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
        $point0 = new Point(0, 0);
        $point1 = new Point(1, 1);
        $multipoint = new MultiPoint([$point0, $point1]);

        $this->assertEquals($point0, $multipoint[0]);
        $this->assertEquals($point1, $multipoint[1]);
        $point2 = new Point(2, 2);

        $multipoint[] = $point2;
        $this->assertEquals($point2, $multipoint[2]);

        unset($multipoint[0]);
        $this->assertNull($multipoint[0]);
        $this->assertEquals($point1, $multipoint[1]);
        $this->assertEquals($point2, $multipoint[2]);

        $point100 = new Point(100, 100);
        $multipoint[100] = $point100;
        $this->assertEquals($point100, $multipoint[100]);

        $this->expectException(InvalidArgumentException::class);
        $multipoint[] = 1;

    }

    public function testPrependPoint() {
        $point1 = new Point(1, 1);
        $point2 = new Point(2, 2);
        $multipoint = new MultiPoint([$point1, $point2]);

        $point0 = new Point(0, 0);
        $multipoint->prependPoint($point0);

        $this->assertEquals($point0, $multipoint->getPoints()[0]);
        $this->assertEquals($point1, $multipoint->getPoints()[1]);
        $this->assertEquals($point2, $multipoint->getPoints()[2]);
    }

    public function testAppendPoint() {
        $point0 = new Point(0, 0);
        $point1 = new Point(1, 1);
        $multipoint = new MultiPoint([$point0, $point1]);

        $point2 = new Point(2, 2);
        $multipoint->appendPoint($point2);

        $this->assertEquals($point0, $multipoint->getPoints()[0]);
        $this->assertEquals($point1, $multipoint->getPoints()[1]);
        $this->assertEquals($point2, $multipoint->getPoints()[2]);
    }

    public function testInsertPoint() {
        $point1 = new Point(1, 1);
        $point3 = new Point(3, 3);
        $multipoint = new MultiPoint([$point1, $point3]);

        $point2 = new Point(2, 2);
        $multipoint->insertPoint(1, $point2);

        $this->assertEquals($point1, $multipoint->getPoints()[0]);
        $this->assertEquals($point2, $multipoint->getPoints()[1]);
        $this->assertEquals($point3, $multipoint->getPoints()[2]);

        $this->expectException(InvalidArgumentException::class);
        $multipoint->insertPoint(100, new Point(100,100));
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
