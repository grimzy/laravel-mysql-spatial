<?php

use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiLineString;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class MultiLineStringTest extends BaseTestCase
{
    public function testFromWKT()
    {
        $multilinestring = MultiLineString::fromWKT('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))');
        $this->assertInstanceOf(MultiLineString::class, $multilinestring);

        $this->assertSame(2, $multilinestring->count());
    }

    public function testToWKT()
    {
        $collection = new LineString([
            new Point(0, 0),
            new Point(0, 1),
            new Point(1, 1),
            new Point(1, 0),
            new Point(0, 0),
        ]);

        $multilinestring = new MultiLineString([$collection]);

        $this->assertSame('MULTILINESTRING((0 0,1 0,1 1,0 1,0 0))', $multilinestring->toWKT());
    }

    public function testFromJson()
    {
        $multiLineString = MultiLineString::fromJson('{"type":"MultiLineString","coordinates":[[[1,1],[1,2],[1,3]],[[2,1],[2,2],[2,3]]]}');
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

    public function testInvalidGeoJsonException()
    {
        $this->assertException(
            \Grimzy\LaravelMysqlSpatial\Exceptions\InvalidGeoJsonException::class,
            sprintf('Expected %s, got %s', GeoJson\Geometry\MultiLineString::class, GeoJson\Geometry\Point::class)
        );
        MultiLineString::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
    }

    public function testJsonSerialize()
    {
        $multilinestring = MultiLineString::fromWKT('MULTILINESTRING((0 0,1 1,1 2),(2 3,3 2,5 4))');

        $this->assertInstanceOf(\GeoJson\Geometry\MultiLineString::class, $multilinestring->jsonSerialize());
        $this->assertSame('{"type":"MultiLineString","coordinates":[[[0,0],[1,1],[1,2]],[[2,3],[3,2],[5,4]]]}', json_encode($multilinestring));
    }

    public function testInvalidArgumentExceptionAtLeastOneEntry()
    {
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\MultiLineString must contain at least 1 entry'
        );
        $multilinestring = new MultiLineString([]);
    }

    public function testInvalidArgumentExceptionNotArrayOfLineString()
    {
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\MultiLineString must be a collection of Grimzy\LaravelMysqlSpatial\Types\LineString'
        );
        $multilinestring = new MultiLineString([
            new LineString([new Point(0, 0), new Point(1, 1)]),
            new Point(0, 1),
        ]);
    }

    public function testArrayAccess()
    {
        $linestring0 = new LineString([
            new Point(0, 0),
            new Point(1, 1),
        ]);
        $linestring1 = new LineString([
            new Point(1, 1),
            new Point(2, 2),
        ]);

        $multilinestring = new MultiLineString([$linestring0, $linestring1]);

        // assert getting
        $this->assertEquals($linestring0, $multilinestring[0]);
        $this->assertEquals($linestring1, $multilinestring[1]);

        // assert setting
        $linestring2 = new LineString([
            new Point(2, 2),
            new Point(3, 3),
        ]);
        $multilinestring[] = $linestring2;
        $this->assertEquals($linestring2, $multilinestring[2]);

        // assert invalid
        $this->assertException(
            InvalidArgumentException::class,
            'Grimzy\LaravelMysqlSpatial\Types\MultiLineString must be a collection of Grimzy\LaravelMysqlSpatial\Types\LineString'
        );
        $multilinestring[] = 1;
    }
}
