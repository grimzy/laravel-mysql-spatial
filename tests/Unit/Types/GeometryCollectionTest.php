<?php

use Grimzy\LaravelMysqlSpatial\Types\GeometryCollection;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;

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
        $this->assertEquals(
            'GEOMETRYCOLLECTION(LINESTRING(0 0,1 0,1 1,0 1,0 0),POINT(200 100))',
            $this->getGeometryCollection()->toWKT()
        );
    }

    public function testJsonSerialize()
    {
        $this->assertInstanceOf(
            \GeoJson\Geometry\GeometryCollection::class,
            $this->getGeometryCollection()->jsonSerialize()
        );

        $this->assertSame(
            '{"type":"GeometryCollection","geometries":[{"type":"LineString","coordinates":[[0,0],[1,0],[1,1],[0,1],[0,0]]},{"type":"Point","coordinates":[200,100]}]}',
            json_encode($this->getGeometryCollection()->jsonSerialize())
        );
    }

    public function testInvalidArgumentExceptionNotArrayGeometries() {
        $this->assertException(InvalidArgumentException::class);
        $geometrycollection = new GeometryCollection([
            $this->getPoint(),
            1
        ]);
    }

    private function getGeometryCollection() {
        return new GeometryCollection([$this->getLineString(), $this->getPoint()]);
    }

    private function getLineString() {
        return new LineString(
            [
                new Point(0, 0),
                new Point(0, 1),
                new Point(1, 1),
                new Point(1, 0),
                new Point(0, 0),
            ]
        );
    }

    private function getPoint() {
        return new Point(100, 200);
    }
}
