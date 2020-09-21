<?php

use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;

class MultiPolygonTest extends BaseTestCase
{
    public function testFromWKT()
    {
        $polygon = MultiPolygon::fromWKT('MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))');
        $this->assertInstanceOf(MultiPolygon::class, $polygon);

        $this->assertEquals(2, $polygon->count());

        $polygon = MultiPolygon::fromWKT('MULTIPOLYGON (((30 20, 45 40, 10 40, 30 20)), ((15 5, 40 10, 10 20, 5 10, 15 5)))');
        $this->assertInstanceOf(MultiPolygon::class, $polygon);

        $this->assertEquals(2, $polygon->count());

        $this->assertEquals('MULTIPOLYGON(((30 20,45 40,10 40,30 20)),((15 5,40 10,10 20,5 10,15 5)))', $polygon->toWKT());
    }

    public function testToWKT()
    {
        $this->assertEquals('MULTIPOLYGON(((0 0,1 0,1 1,0 1,0 0),(10 10,20 10,20 20,10 20,10 10)),((100 100,200 100,200 200,100 200,100 100)))', $this->getMultiPolygon()->toWKT());
    }

    public function testGetPolygons()
    {
        $polygon = MultiPolygon::fromWKT('MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))');

        $this->assertInstanceOf(Polygon::class, $polygon->getPolygons()[0]);
    }

    public function testIssue12()
    {
        $polygon = MultiPolygon::fromWKT('MULTIPOLYGON(((-80.214554 25.769598 0 0,-80.2147 25.774514 0 0,-80.212983 25.77456 0 0,-80.212977 25.773597 0 0,-80.211448 25.773655 0 0,-80.211498 25.774579 0 0,-80.209432 25.774665 0 0,-80.209392 25.773667 0 0,-80.204387 25.773834 0 0,-80.199383 25.774324 0 0,-80.197718 25.774031 0 0,-80.197757 25.774975 0 0,-80.193655 25.775108 0 0,-80.193623 25.774134 0 0,-80.191855 25.772551 0 0,-80.193442 25.76969 0 0,-80.192231 25.768345 0 0,-80.192879 25.758009 0 0,-80.196301 25.759985 0 0,-80.195608 25.76152 0 0,-80.198856 25.761454 0 0,-80.200646 25.763287 0 0,-80.20401 25.763164 0 0,-80.204023 25.76367 0 0,-80.205673 25.763141 0 0,-80.214326 25.762935 0 0,-80.214451 25.765883 0 0,-80.214539 25.768649 0 0,-80.216203 25.76858 0 0,-80.214554 25.769598 0 0)))');

        $this->assertInstanceOf(MultiPolygon::class, $polygon);
    }

    public function testFromJson()
    {
        $multiPolygon = MultiPolygon::fromJson('{"type":"MultiPolygon","coordinates":[[[[1,1],[1,2],[2,2],[2,1],[1,1]]],[[[0,0],[0,1],[1,1],[1,0],[0,0]]]]}');
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

    public function testInvalidGeoJsonException()
    {
        $this->assertException(
            \Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException::class,
            sprintf('Expected %s, got %s', GeoJson\Geometry\MultiPolygon::class, GeoJson\Geometry\Point::class)
        );
        MultiPolygon::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
    }

    public function testJsonSerialize()
    {
        $this->assertInstanceOf(\GeoJson\Geometry\MultiPolygon::class, $this->getMultiPolygon()->jsonSerialize());
        $this->assertSame('{"type":"MultiPolygon","coordinates":[[[[0,0],[1,0],[1,1],[0,1],[0,0]],[[10,10],[20,10],[20,20],[10,20],[10,10]]],[[[100,100],[200,100],[200,200],[100,200],[100,100]]]]}', json_encode($this->getMultiPolygon()));
    }

    public function testInvalidArgumentExceptionAtLeastOneEntry()
    {
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\MultiPolygon must contain at least 1 entry'
        );
        $multipolygon = new MultiPolygon([]);
    }

    public function testInvalidArgumentExceptionNotArrayOfPolygon()
    {
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\MultiPolygon must be a collection of Grimzy\LaravelMysqlSpatial\Types\Polygon'
        );
        $multipolygon = new MultiPolygon([
            $this->getPolygon1(),
            $this->getLineString1(),
        ]);
    }

    public function testArrayAccess()
    {
        $polygon0 = $this->getPolygon1();
        $polygon1 = $this->getPolygon2();

        $multipolygon = new MultiPolygon([$polygon0, $polygon1]);

        // assert getting
        $this->assertEquals($polygon0, $multipolygon[0]);
        $this->assertEquals($polygon1, $multipolygon[1]);

        // assert setting
        $polygon2 = $this->getPolygon3();
        $multipolygon[] = $polygon2;
        $this->assertEquals($polygon2, $multipolygon[2]);

        // assert invalid
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\MultiPolygon must be a collection of Grimzy\LaravelMysqlSpatial\Types\Polygon'
        );
        $multipolygon[] = 1;
    }

    private function getMultiPolygon()
    {
        return new MultiPolygon([$this->getPolygon1(), $this->getPolygon2()]);
    }

    private function getLineString1()
    {
        return new LineString([
            new Point(0, 0),
            new Point(0, 1),
            new Point(1, 1),
            new Point(1, 0),
            new Point(0, 0),
        ]);
    }

    private function getLineString2()
    {
        return new LineString([
            new Point(10, 10),
            new Point(10, 20),
            new Point(20, 20),
            new Point(20, 10),
            new Point(10, 10),
        ]);
    }

    private function getLineString3()
    {
        return new LineString([
            new Point(100, 100),
            new Point(100, 200),
            new Point(200, 200),
            new Point(200, 100),
            new Point(100, 100),
        ]);
    }

    private function getPolygon1()
    {
        return new Polygon([$this->getLineString1(), $this->getLineString2()]);
    }

    private function getPolygon2()
    {
        return new Polygon([$this->getLineString3()]);
    }

    private function getPolygon3()
    {
        return new Polygon([
            new LineString([
                new Point(10, 10),
                new Point(10, 20),
                new Point(20, 20),
                new Point(20, 10),
                new Point(10, 10),
            ]),
        ]);
    }
}
