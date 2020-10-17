<?php

use Grimzy\LaravelMysqlSpatial\Types\GeometryCollection;
use Grimzy\LaravelMysqlSpatial\Types\GeometryInterface;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;

class GeometryCollectionTest extends BaseTestCase
{
    public function testFromWKT()
    {
        /**
         * @var GeometryCollection
         */
        $geometryCollection = GeometryCollection::fromWKT('GEOMETRYCOLLECTION(POINT(2 3),LINESTRING(2 3,3 4))');
        $this->assertInstanceOf(GeometryCollection::class, $geometryCollection);

        $this->assertEquals(2, $geometryCollection->count());
        $this->assertInstanceOf(Point::class, $geometryCollection->getGeometries()[0]);
        $this->assertInstanceOf(LineString::class, $geometryCollection->getGeometries()[1]);
    }

    public function testToWKT()
    {
        $this->assertEquals('GEOMETRYCOLLECTION(LINESTRING(0 0,1 0,1 1,0 1,0 0),POINT(200 100))', $this->getGeometryCollection()->toWKT());
    }

    public function testJsonSerialize()
    {
        $this->assertInstanceOf(\GeoJson\Geometry\GeometryCollection::class, $this->getGeometryCollection()->jsonSerialize());

        $this->assertSame('{"type":"GeometryCollection","geometries":[{"type":"LineString","coordinates":[[0,0],[1,0],[1,1],[0,1],[0,0]]},{"type":"Point","coordinates":[200,100]}]}', json_encode($this->getGeometryCollection()->jsonSerialize()));
    }

    public function testCanCreateEmptyGeometryCollection()
    {
        $geometryCollection = new GeometryCollection([]);
        $this->assertInstanceOf(GeometryCollection::class, $geometryCollection);
    }

    public function testFromWKTWithEmptyGeometryCollection()
    {
        /**
         * @var GeometryCollection
         */
        $geometryCollection = GeometryCollection::fromWKT('GEOMETRYCOLLECTION()');
        $this->assertInstanceOf(GeometryCollection::class, $geometryCollection);

        $this->assertEquals(0, $geometryCollection->count());
    }

    public function testToWKTWithEmptyGeometryCollection()
    {
        $this->assertEquals('GEOMETRYCOLLECTION()', (new GeometryCollection([]))->toWKT());
    }

    public function testInvalidArgumentExceptionNotArrayGeometries()
    {
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\GeometryCollection must be a collection of Grimzy\LaravelMysqlSpatial\Types\GeometryInterface'
        );
        $geometrycollection = new GeometryCollection([
            $this->getPoint(),
            1,
        ]);
    }

    public function testToArray()
    {
        $geometryCollection = $this->getGeometryCollection();

        $this->assertInternalType('array', $geometryCollection->toArray());
    }

    public function testIteratorAggregate()
    {
        $geometryCollection = $this->getGeometryCollection();

        foreach ($geometryCollection as $value) {
            $this->assertInstanceOf(GeometryInterface::class, $value);
        }
    }

    public function testArrayAccess()
    {
        $linestring = $this->getLineString();
        $point = $this->getPoint();
        $geometryCollection = new GeometryCollection([$linestring, $point]);

        // assert getting
        $this->assertEquals($linestring, $geometryCollection[0]);
        $this->assertEquals($point, $geometryCollection[1]);

        // assert setting
        $polygon = $this->getPolygon();
        $geometryCollection[] = $polygon;
        $this->assertEquals($polygon, $geometryCollection[2]);

        // assert unset
        unset($geometryCollection[0]);
        $this->assertNull($geometryCollection[0]);
        $this->assertEquals($point, $geometryCollection[1]);
        $this->assertEquals($polygon, $geometryCollection[2]);

        // assert insert
        $point100 = new Point(100, 100);
        $geometryCollection[100] = $point100;
        $this->assertEquals($point100, $geometryCollection[100]);

        // assert invalid
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\GeometryCollection must be a collection of Grimzy\LaravelMysqlSpatial\Types\GeometryInterface'
        );
        $geometryCollection[] = 1;
    }

    public function testFromJson()
    {
        $geometryCollection = GeometryCollection::fromJson('{"type":"FeatureCollection","features":[{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":[1,2]}},{"type":"Feature","properties":{},"geometry":{"type":"Point","coordinates":[3,4]}}]}');
        $this->assertInstanceOf(GeometryCollection::class, $geometryCollection);
        $geometryCollectionPoints = $geometryCollection->getGeometries();
        $this->assertEquals(2, count($geometryCollectionPoints));
        $this->assertEquals(new Point(2, 1), $geometryCollectionPoints[0]);
        $this->assertEquals(new Point(4, 3), $geometryCollectionPoints[1]);
    }

    public function testInvalidGeoJsonException()
    {
        $this->assertException(
            \Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException::class,
            sprintf('Expected %s, got %s', GeoJson\Feature\FeatureCollection::class, GeoJson\Geometry\Point::class)
        );
        GeometryCollection::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
    }

    private function getGeometryCollection()
    {
        return new GeometryCollection([$this->getLineString(), $this->getPoint()]);
    }

    private function getLineString()
    {
        return new LineString([
            new Point(0, 0),
            new Point(0, 1),
            new Point(1, 1),
            new Point(1, 0),
            new Point(0, 0),
        ]);
    }

    private function getPoint()
    {
        return new Point(100, 200);
    }

    private function getPolygon()
    {
        return new Polygon([
            new LineString([
                new Point(0, 0),
                new Point(0, 1),
                new Point(1, 1),
                new Point(1, 0),
                new Point(0, 0),
            ]),
        ]);
    }
}
