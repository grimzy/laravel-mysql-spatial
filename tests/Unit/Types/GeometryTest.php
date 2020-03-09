<?php

use Grimzy\LaravelMysqlSpatial\Exceptions\UnknownWKTTypeException;
use Grimzy\LaravelMysqlSpatial\Types\Geometry;
use Grimzy\LaravelMysqlSpatial\Types\GeometryCollection;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiLineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiPoint;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;

class GeometryTest extends BaseTestCase
{
    public function testGetWKTArgument()
    {
        $this->assertEquals('0 0', Geometry::getWKTArgument('POINT(0 0)'));
        $this->assertEquals('0 0,1 1,1 2', Geometry::getWKTArgument('LINESTRING(0 0,1 1,1 2)'));
        $this->assertEquals('(0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1)', Geometry::getWKTArgument('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))'));
        $this->assertEquals('(0 0),(1 2)', Geometry::getWKTArgument('MULTIPOINT((0 0),(1 2))'));
        $this->assertEquals('(0 0,1 1,1 2),(2 3,3 2,5 4)', Geometry::getWKTArgument('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))'));
        $this->assertEquals('((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1))', Geometry::getWKTArgument('MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))'));
        $this->assertEquals('POINT(2 3),LINESTRING(2 3,3 4)', Geometry::getWKTArgument('GEOMETRYCOLLECTION(POINT(2 3),LINESTRING(2 3,3 4))'));
    }

    public function testGetWKTClass()
    {
        $this->assertEquals(Point::class, Geometry::getWKTClass('POINT(0 0)'));
        $this->assertEquals(LineString::class, Geometry::getWKTClass('LINESTRING(0 0,1 1,1 2)'));
        $this->assertEquals(Polygon::class, Geometry::getWKTClass('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))'));
        $this->assertEquals(MultiPoint::class, Geometry::getWKTClass('MULTIPOINT((0 0),(1 2))'));
        $this->assertEquals(MultiLineString::class, Geometry::getWKTClass('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))'));
        $this->assertEquals(MultiPolygon::class, Geometry::getWKTClass('MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))'));
        $this->assertEquals(GeometryCollection::class, Geometry::getWKTClass('GEOMETRYCOLLECTION(POINT(2 3),LINESTRING(2 3,3 4))'));
        $this->assertException(
            UnknownWKTTypeException::class,
            'Type was TRIANGLE'
        );
        Geometry::getWKTClass('TRIANGLE((0 0, 0 9, 9 0, 0 0))');
    }

    public function testGetWKBClass()
    {
        $prefix = "\0\0\0\0";

        $this->assertInstanceOf(Point::class, Geometry::fromWKB($prefix.'0101000000000000000000f03f0000000000000040'));

        $this->assertInstanceOf(LineString::class, Geometry::fromWKB($prefix.'010200000002000000000000000000f03f000000000000004000000000000008400000000000001040'));
        $this->assertInstanceOf(Polygon::class, Geometry::fromWKB($prefix.'01030000000100000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f0000000000000040'));
        $this->assertInstanceOf(MultiPoint::class, Geometry::fromWKB($prefix.'0104000000020000000101000000000000000000f03f0000000000000040010100000000000000000008400000000000001040'));
        $this->assertInstanceOf(MultiLineString::class, Geometry::fromWKB($prefix.'010500000001000000010200000002000000000000000000f03f000000000000004000000000000008400000000000001040'));
        $this->assertInstanceOf(MultiLineString::class, Geometry::fromWKB($prefix.'010500000002000000010200000002000000000000000000f03f000000000000004000000000000008400000000000001040010200000002000000000000000000144000000000000018400000000000001c400000000000002040'));
        $this->assertInstanceOf(MultiPolygon::class, Geometry::fromWKB($prefix.'01060000000200000001030000000100000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f000000000000004001030000000300000004000000000000000000f03f00000000000000400000000000000840000000000000104000000000000014400000000000001840000000000000f03f000000000000004004000000000000000000264000000000000028400000000000002a400000000000002c400000000000002e4000000000000030400000000000002640000000000000284004000000000000000000354000000000000036400000000000003740000000000000384000000000000039400000000000003a4000000000000035400000000000003640'));
        $this->assertInstanceOf(GeometryCollection::class, Geometry::fromWKB($prefix.'0107000000010000000101000000000000000000f03f0000000000000040'));
        $this->assertInstanceOf(GeometryCollection::class, Geometry::fromWKB($prefix.'0107000000020000000101000000000000000000f03f0000000000000040010200000002000000000000000000f03f000000000000004000000000000008400000000000001040'));

        $this->assertInstanceOf(Point::class, Geometry::fromWKB($prefix.'0101000000000000000000f03f0000000000000040'));
    }

    public function testFromJsonPoint()
    {
        $point = Geometry::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(1.2, $point->getLat());
        $this->assertEquals(3.4, $point->getLng());
    }

    public function testFromJsonLineString()
    {
        $lineString = Geometry::fromJson('{"type": "LineString","coordinates":[[1,1],[2,2]]}');
        $this->assertInstanceOf(LineString::class, $lineString);
        $lineStringPoints = $lineString->getGeometries();
        $this->assertEquals(new Point(1, 1), $lineStringPoints[0]);
        $this->assertEquals(new Point(2, 2), $lineStringPoints[1]);
    }

    public function testFromJsonPolygon()
    {
        $polygon1 = Geometry::fromJson('{"type": "Polygon","coordinates":[[[1,1],[2,1],[2,2],[1,2],[1,1]]]}');
        $this->assertInstanceOf(Polygon::class, $polygon1);
        $polygonLineStrings1 = $polygon1->getGeometries();
        $this->assertEquals(1, count($polygonLineStrings1));
        $this->assertEquals(new Point(1, 1), $polygonLineStrings1[0][0]);
        $this->assertEquals(new Point(1, 2), $polygonLineStrings1[0][1]);
        $this->assertEquals(new Point(2, 2), $polygonLineStrings1[0][2]);
        $this->assertEquals(new Point(2, 1), $polygonLineStrings1[0][3]);
        $this->assertEquals(new Point(1, 1), $polygonLineStrings1[0][4]);

        $polygon2 = Geometry::fromJson('{"type":"Polygon","coordinates":[[[1,1],[2,1],[2,2],[1,2],[1,1]],[[1.2,1.2],[1.6,1.2],[1.6,1.8],[1.2,1.8],[1.2,1.2]]]}');
        $this->assertInstanceOf(Polygon::class, $polygon2);
        $polygonLineStrings2 = $polygon2->getGeometries();
        $this->assertEquals(2, count($polygonLineStrings2));
        $this->assertEquals(new Point(1, 1), $polygonLineStrings2[0][0]);
        $this->assertEquals(new Point(1, 2), $polygonLineStrings2[0][1]);
        $this->assertEquals(new Point(2, 2), $polygonLineStrings2[0][2]);
        $this->assertEquals(new Point(2, 1), $polygonLineStrings2[0][3]);
        $this->assertEquals(new Point(1, 1), $polygonLineStrings2[0][4]);
        $this->assertEquals(new Point(1.2, 1.2), $polygonLineStrings2[1][0]);
        $this->assertEquals(new Point(1.2, 1.6), $polygonLineStrings2[1][1]);
        $this->assertEquals(new Point(1.8, 1.6), $polygonLineStrings2[1][2]);
        $this->assertEquals(new Point(1.8, 1.2), $polygonLineStrings2[1][3]);
        $this->assertEquals(new Point(1.2, 1.2), $polygonLineStrings2[1][4]);
    }

    public function testFromJsonMultiPoint()
    {
        $multiPoint = Geometry::fromJson('{"type":"MultiPoint","coordinates":[[1,1],[2,1],[2,2]]}');
        $this->assertInstanceOf(MultiPoint::class, $multiPoint);
        $multiPointPoints = $multiPoint->getGeometries();
        $this->assertEquals(3, count($multiPointPoints));
        $this->assertEquals(new Point(1, 1), $multiPointPoints[0]);
        $this->assertEquals(new Point(1, 2), $multiPointPoints[1]);
        $this->assertEquals(new Point(2, 2), $multiPointPoints[2]);
    }

    public function testFromJsonMultiLineString()
    {
        $multiLineString = Geometry::fromJson('{"type":"MultiLineString","coordinates":[[[1,1],[1,2],[1,3]],[[2,1],[2,2],[2,3]]]}');
        $this->assertInstanceOf(MultiLineString::class, $multiLineString);
        $multiLineStringLineStrings = $multiLineString->getGeometries();
        $this->assertEquals(2, count($multiLineStringLineStrings));
        $this->assertEquals(new Point(1, 1), $multiLineStringLineStrings[0][0]);
        $this->assertEquals(new Point(2, 1), $multiLineStringLineStrings[0][1]);
        $this->assertEquals(new Point(3, 1), $multiLineStringLineStrings[0][2]);
        $this->assertEquals(new Point(1, 2), $multiLineStringLineStrings[1][0]);
        $this->assertEquals(new Point(2, 2), $multiLineStringLineStrings[1][1]);
        $this->assertEquals(new Point(3, 2), $multiLineStringLineStrings[1][2]);
    }

    public function testFromJsonMultiPolygon()
    {
        $multiPolygon = Geometry::fromJson('{"type":"MultiPolygon","coordinates":[[[[1,1],[1,2],[2,2],[2,1],[1,1]]],[[[0,0],[0,1],[1,1],[1,0],[0,0]]]]}');
        $this->assertInstanceOf(MultiPolygon::class, $multiPolygon);
        $multiPolygonPolygons = $multiPolygon->getGeometries();
        $this->assertEquals(2, count($multiPolygonPolygons));
        $this->assertEquals(new Polygon([new LineString([
            new Point(1, 1),
            new Point(2, 1),
            new Point(2, 2),
            new Point(1, 2),
            new Point(1, 1),
        ])]), $multiPolygonPolygons[0]);
        $this->assertEquals(new Polygon([new LineString([
            new Point(0, 0),
            new Point(1, 0),
            new Point(1, 1),
            new Point(0, 1),
            new Point(0, 0),
        ])]), $multiPolygonPolygons[1]);
    }

    public function testFromJsonPointFeature()
    {
        $point = Geometry::fromJson('{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":[3.4,1.2]}}');
        $this->assertInstanceOf(Point::class, $point);
        $this->assertEquals(1.2, $point->getLat());
        $this->assertEquals(3.4, $point->getLng());
    }

    public function testFromJsonMultiPointFeatureCollection()
    {
        $geometryCollection = Geometry::fromJson('{"type":"FeatureCollection","features":[{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":[1,2]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":[3,4]}}]}');
        $this->assertInstanceOf(GeometryCollection::class, $geometryCollection);
        $geometryCollectionPoints = $geometryCollection->getGeometries();
        $this->assertEquals(2, count($geometryCollectionPoints));
        $this->assertEquals(new Point(2, 1), $geometryCollectionPoints[0]);
        $this->assertEquals(new Point(4, 3), $geometryCollectionPoints[1]);
    }

    public function testToJson()
    {
        $point = new Point(1, 1);

        $this->assertSame('{"type":"Point","coordinates":[1,1]}', $point->toJson());
    }
}
