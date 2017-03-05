<?php

use Grimzy\LaravelSpatial\Types\LineString;
use Grimzy\LaravelSpatial\Types\MultiPolygon;
use Grimzy\LaravelSpatial\Types\Point;
use Grimzy\LaravelSpatial\Types\Polygon;

class MultiPolygonTest extends BaseTestCase
{
    /**
     * @var MultiPolygon
     */
    private $multiPolygon;

    protected function setUp()
    {
        $collection1 = new LineString(
            [
                new Point(0, 0),
                new Point(0, 1),
                new Point(1, 1),
                new Point(1, 0),
                new Point(0, 0)
            ]
        );

        $collection2 = new LineString(
            [
                new Point(10, 10),
                new Point(10, 20),
                new Point(20, 20),
                new Point(20, 10),
                new Point(10, 10)
            ]
        );

        $polygon1 = new Polygon([$collection1, $collection2]);

        $collection3 = new LineString(
            [
                new Point(100, 100),
                new Point(100, 200),
                new Point(200, 200),
                new Point(200, 100),
                new Point(100, 100)
            ]
        );


        $polygon2 = new Polygon([$collection3]);

        $this->multiPolygon = new MultiPolygon([$polygon1, $polygon2]);
    }

    public function testFromWKT()
    {
        $polygon = MultiPolygon::fromWKT(
            'MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))'
        );
        $this->assertInstanceOf(MultiPolygon::class, $polygon);

        $this->assertEquals(2, $polygon->count());
    }


    public function testToWKT()
    {
        $this->assertEquals(
            'MULTIPOLYGON(((0 0,1 0,1 1,0 1,0 0),(10 10,20 10,20 20,10 20,10 10)),((100 100,200 100,200 200,100 200,100 100)))',
            $this->multiPolygon->toWKT()
        );
    }

    public function testGetPolygons()
    {
        $polygon = MultiPolygon::fromWKT(
          'MULTIPOLYGON(((0 0,4 0,4 4,0 4,0 0),(1 1,2 1,2 2,1 2,1 1)), ((-1 -1,-1 -2,-2 -2,-2 -1,-1 -1)))'
        );

        $this->assertInstanceOf(Polygon::class, $polygon->getPolygons()[0]);
    }

    public function testIssue12()
    {
        $polygon = MultiPolygon::fromWKT(
          'MULTIPOLYGON(((-80.214554 25.769598 0 0,-80.2147 25.774514 0 0,-80.212983 25.77456 0 0,-80.212977 25.773597 0 0,-80.211448 25.773655 0 0,-80.211498 25.774579 0 0,-80.209432 25.774665 0 0,-80.209392 25.773667 0 0,-80.204387 25.773834 0 0,-80.199383 25.774324 0 0,-80.197718 25.774031 0 0,-80.197757 25.774975 0 0,-80.193655 25.775108 0 0,-80.193623 25.774134 0 0,-80.191855 25.772551 0 0,-80.193442 25.76969 0 0,-80.192231 25.768345 0 0,-80.192879 25.758009 0 0,-80.196301 25.759985 0 0,-80.195608 25.76152 0 0,-80.198856 25.761454 0 0,-80.200646 25.763287 0 0,-80.20401 25.763164 0 0,-80.204023 25.76367 0 0,-80.205673 25.763141 0 0,-80.214326 25.762935 0 0,-80.214451 25.765883 0 0,-80.214539 25.768649 0 0,-80.216203 25.76858 0 0,-80.214554 25.769598 0 0)))'
        );

        $this->assertInstanceOf(MultiPolygon::class, $polygon);
    }

    public function testJsonSerialize()
    {
        $this->assertInstanceOf(\GeoJson\Geometry\MultiPolygon::class, $this->multiPolygon->jsonSerialize());
        $this->assertSame(
            '{"type":"MultiPolygon","coordinates":[[[[0,0],[1,0],[1,1],[0,1],[0,0]],[[10,10],[20,10],[20,20],[10,20],[10,10]]],[[[100,100],[200,100],[200,200],[100,200],[100,100]]]]}',
            json_encode($this->multiPolygon)
        );
    }
}
