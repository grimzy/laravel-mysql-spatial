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

    public function testFromJson()
    {
        $multiPoint = MultiPoint::fromJson('{"type":"MultiPoint","coordinates":[[1,1],[2,1],[2,2]]}');
        $this->assertInstanceOf(MultiPoint::class, $multiPoint);
        $multiPointPoints = $multiPoint->getGeometries();
        $this->assertEquals(3, count($multiPointPoints));
        $this->assertEquals(new Point(1, 1), $multiPointPoints[0]);
        $this->assertEquals(new Point(1, 2), $multiPointPoints[1]);
        $this->assertEquals(new Point(2, 2), $multiPointPoints[2]);
    }

    public function testInvalidGeoJsonException()
    {
        $this->setExpectedException(\Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException::class);
        MultiPoint::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
    }

    public function testJsonSerialize()
    {
        $collection = [new Point(0, 0), new Point(0, 1), new Point(1, 1)];

        $multipoint = new MultiPoint($collection);

        $this->assertInstanceOf(\GeoJson\Geometry\MultiPoint::class, $multipoint->jsonSerialize());
        $this->assertSame('{"type":"MultiPoint","coordinates":[[0,0],[1,0],[1,1]]}', json_encode($multipoint));
    }

    public function testInvalidArgumentExceptionAtLeastOneEntry()
    {
        $this->assertException(InvalidArgumentException::class);
        $multipoint = new MultiPoint([]);
    }

    public function testInvalidArgumentExceptionNotArrayOfLineString()
    {
        $this->assertException(InvalidArgumentException::class);
        $multipoint = new MultiPoint([
            new Point(0, 0),
            1,
        ]);
    }

    public function testArrayAccess()
    {
        $point0 = new Point(0, 0);
        $point1 = new Point(1, 1);
        $multipoint = new MultiPoint([$point0, $point1]);

        // assert getting
        $this->assertEquals($point0, $multipoint[0]);
        $this->assertEquals($point1, $multipoint[1]);

        // assert setting
        $point2 = new Point(2, 2);
        $multipoint[] = $point2;
        $this->assertEquals($point2, $multipoint[2]);

        // assert invalid
        $this->assertException(InvalidArgumentException::class);
        $multipoint[] = 1;
    }

    public function testDeprecatedPrependPoint()
    {
        $point1 = new Point(1, 1);
        $point2 = new Point(2, 2);
        $multipoint = new MultiPoint([$point1, $point2]);

        $point0 = new Point(0, 0);
        $multipoint->prependPoint($point0);

        $this->assertEquals($point0, $multipoint[0]);
        $this->assertEquals($point1, $multipoint[1]);
        $this->assertEquals($point2, $multipoint[2]);
    }

    public function testDeprecatedAppendPoint()
    {
        $point0 = new Point(0, 0);
        $point1 = new Point(1, 1);
        $multipoint = new MultiPoint([$point0, $point1]);

        $point2 = new Point(2, 2);
        $multipoint->appendPoint($point2);

        $this->assertEquals($point0, $multipoint[0]);
        $this->assertEquals($point1, $multipoint[1]);
        $this->assertEquals($point2, $multipoint[2]);
    }

    public function testDeprecatedInsertPoint()
    {
        $point1 = new Point(1, 1);
        $point3 = new Point(3, 3);
        $multipoint = new MultiPoint([$point1, $point3]);

        $point2 = new Point(2, 2);
        $multipoint->insertPoint(1, $point2);

        $this->assertEquals($point1, $multipoint[0]);
        $this->assertEquals($point2, $multipoint[1]);
        $this->assertEquals($point3, $multipoint[2]);

        $this->assertException(InvalidArgumentException::class);
        $multipoint->insertPoint(100, new Point(100, 100));
    }
}
