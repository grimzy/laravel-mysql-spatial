<?php

use Grimzy\LaravelMysqlSpatial\SpatialServiceProvider;
use Grimzy\LaravelMysqlSpatial\Types\GeometryCollection;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Grimzy\LaravelMysqlSpatial\Types\MultiPoint;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class SpatialTest extends BaseTestCase
{
    protected $after_fix = false;

    public static function setUpBeforeClass()
    {
        self::cleanDatabase(true);

        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        self::cleanDatabase();

        parent::tearDownAfterClass();
    }

    /**
     * Deletes the database.
     *
     * @param bool $recreate If true, then creates the database after deletion
     */
    private static function cleanDatabase($recreate = false)
    {
        $database = env('DB_DATABASE');

        try {
            $pdo = new PDO(
                sprintf(
                    'mysql:host=%s;port=%d;',
                    env('DB_HOST'),
                    env('DB_PORT')
                ),
                env('DB_USERNAME'),
                env('DB_PASSWORD')
            );

            $pdo->exec(sprintf('DROP DATABASE IF EXISTS %s', $database));
            if ($recreate) {
                $pdo->exec(sprintf(
                    'CREATE DATABASE %s CHARACTER SET %s COLLATE %s;',
                    $database,
                    env('DB_CHARSET', 'utf8mb4'),
                    env('DB_COLLATION', 'utf8mb4_unicode_ci')
                ));
            }
        } catch (RuntimeException $exception) {
            throw $exception;
        }
    }

    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php';
        $app->register(SpatialServiceProvider::class);

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql.host', env('DB_HOST'));
        $app['config']->set('database.connections.mysql.port', env('DB_PORT'));
        $app['config']->set('database.connections.mysql.database', env('DB_DATABASE'));
        $app['config']->set('database.connections.mysql.username', env('DB_USERNAME'));
        $app['config']->set('database.connections.mysql.password', env('DB_PASSWORD'));
        $app['config']->set('database.connections.mysql.modes', [
            'ONLY_FULL_GROUP_BY',
            'STRICT_TRANS_TABLES',
            'NO_ZERO_IN_DATE',
            'NO_ZERO_DATE',
            'ERROR_FOR_DIVISION_BY_ZERO',
            'NO_ENGINE_SUBSTITUTION',
        ]);

        return $app;
    }

    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->after_fix = $this->isMySQL8AfterFix();

        $this->onMigrations(function ($migrationClass) {
            (new $migrationClass())->up();
        });

//        \DB::listen(function($sql) {
//            var_dump($sql);
//        });
    }

    public function tearDown()
    {
        $this->onMigrations(function ($migrationClass) {
            (new $migrationClass())->down();
        }, true);

        parent::tearDown();
    }

    // MySQL 8.0.4 fixed bug #26941370 and bug #88031
    private function isMySQL8AfterFix()
    {
        $results = DB::select(DB::raw('select version()'));
        $mysql_version = $results[0]->{'version()'};

        return version_compare($mysql_version, '8.0.4', '>=');
    }

    protected function assertDatabaseHas($table, array $data, $connection = null)
    {
        if (method_exists($this, 'seeInDatabase')) {
            $this->seeInDatabase($table, $data, $connection);
        } else {
            parent::assertDatabaseHas($table, $data, $connection);
        }
    }

    protected function assertException($exceptionName)
    {
        if (method_exists(parent::class, 'expectException')) {
            parent::expectException($exceptionName);
        } else {
            $this->setExpectedException($exceptionName);
        }
    }

    private function onMigrations(\Closure $closure, $reverse_sort = false)
    {
        $fileSystem = new Filesystem();
        $classFinder = new Tools\ClassFinder();

        $migrations = $fileSystem->files(__DIR__.'/Migrations');
        $reverse_sort ? rsort($migrations, SORT_STRING) : sort($migrations, SORT_STRING);

        foreach ($migrations as $file) {
            $fileSystem->requireOnce($file);
            $migrationClass = $classFinder->findClass($file);

            $closure($migrationClass);
        }
    }

    public function testSpatialFieldsNotDefinedException()
    {
        $geo = new NoSpatialFieldsModel();
        $geo->geometry = new Point(1, 2);
        $geo->save();

        $this->assertException(\Grimzy\LaravelMysqlSpatial\Exceptions\SpatialFieldsNotDefinedException::class);
        NoSpatialFieldsModel::all();
    }

    public function testInsertPoint()
    {
        $geo = new GeometryModel();
        $geo->location = new Point(1, 2);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertLineString()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);
        $geo->line = new LineString([new Point(1, 1), new Point(2, 2)]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertPolygon()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);
        $geo->shape = Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))');
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertMultiPoint()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);
        $geo->multi_locations = new MultiPoint([new Point(1, 1), new Point(2, 2)]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertMultiPolygon()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);

        $geo->multi_shapes = new MultiPolygon([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
        ]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertGeometryCollection()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);

        $geo->multi_geometries = new GeometryCollection([
            Polygon::fromWKT('POLYGON((0 10,10 10,10 0,0 0,0 10))'),
            Polygon::fromWKT('POLYGON((0 0,0 5,5 5,5 0,0 0))'),
            new Point(0, 0),
        ]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
    }

    public function testInsertEmptyGeometryCollection()
    {
        $geo = new GeometryModel();

        $geo->location = new Point(1, 2);

        $geo->multi_geometries = new GeometryCollection([]);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);

        $geo2 = GeometryModel::find($geo->id);
        $this->assertInstanceOf(GeometryCollection::class, $geo2->multi_geometries);
        $this->assertEquals(0, count($geo2->multi_geometries));
    }

    public function testUpdate()
    {
        $geo = new GeometryModel();
        $geo->location = new Point(1, 2);
        $geo->save();

        $to_update = GeometryModel::all()->first();
        $to_update->location = new Point(2, 3);
        $to_update->save();

        $this->assertDatabaseHas('geometry', ['id' => $to_update->id]);

        $all = GeometryModel::all();
        $this->assertCount(1, $all);

        $updated = $all->first();
        $this->assertInstanceOf(Point::class, $updated->location);
        $this->assertEquals(2, $updated->location->getLat());
        $this->assertEquals(3, $updated->location->getLng());
    }

    public function testDistance()
    {
        $loc1 = new GeometryModel();
        $loc1->location = new Point(1, 1);
        $loc1->save();

        $loc2 = new GeometryModel();
        $loc2->location = new Point(2, 2); // Distance from loc1: 1.4142135623731
        $loc2->save();

        $loc3 = new GeometryModel();
        $loc3->location = new Point(3, 3); // Distance from loc1: 2.8284271247462
        $loc3->save();

        $a = GeometryModel::distance('location', $loc1->location, 2)->get();
        $this->assertCount(2, $a);
        $this->assertTrue($a->contains('location', $loc1->location));
        $this->assertTrue($a->contains('location', $loc2->location));
        $this->assertFalse($a->contains('location', $loc3->location));

        // Excluding self
        $b = GeometryModel::distanceExcludingSelf('location', $loc1->location, 2)->get();
        $this->assertCount(1, $b);
        $this->assertFalse($b->contains('location', $loc1->location));
        $this->assertTrue($b->contains('location', $loc2->location));
        $this->assertFalse($b->contains('location', $loc3->location));

        $c = GeometryModel::distance('location', $loc1->location, 1)->get();
        $this->assertCount(1, $c);
        $this->assertTrue($c->contains('location', $loc1->location));
        $this->assertFalse($c->contains('location', $loc2->location));
        $this->assertFalse($c->contains('location', $loc3->location));
    }

    public function testDistanceSphere()
    {
        $loc1 = new GeometryModel();
        $loc1->location = new Point(40.767864, -73.971732);
        $loc1->save();

        $loc2 = new GeometryModel();
        $loc2->location = new Point(40.767664, -73.971271); // Distance from loc1: 44.741406484588
        $loc2->save();

        $loc3 = new GeometryModel();
        $loc3->location = new Point(40.761434, -73.977619); // Distance from loc1: 870.06424066202
        $loc3->save();

        $a = GeometryModel::distanceSphere('location', $loc1->location, 200)->get();
        $this->assertCount(2, $a);
        $this->assertTrue($a->contains('location', $loc1->location));
        $this->assertTrue($a->contains('location', $loc2->location));
        $this->assertFalse($a->contains('location', $loc3->location));

        // Excluding self
        $b = GeometryModel::distanceSphereExcludingSelf('location', $loc1->location, 200)->get();
        $this->assertCount(1, $b);
        $this->assertFalse($b->contains('location', $loc1->location));
        $this->assertTrue($b->contains('location', $loc2->location));
        $this->assertFalse($b->contains('location', $loc3->location));

        if ($this->after_fix) {
            $c = GeometryModel::distanceSphere('location', $loc1->location, 44.741406484236)->get();
        } else {
            $c = GeometryModel::distanceSphere('location', $loc1->location, 44.741406484587)->get();
        }
        $this->assertCount(1, $c);
        $this->assertTrue($c->contains('location', $loc1->location));
        $this->assertFalse($c->contains('location', $loc2->location));
        $this->assertFalse($c->contains('location', $loc3->location));
    }

    public function testDistanceValue()
    {
        $loc1 = new GeometryModel();
        $loc1->location = new Point(1, 1);
        $loc1->save();

        $loc2 = new GeometryModel();
        $loc2->location = new Point(2, 2); // Distance from loc1: 1.4142135623731
        $loc2->save();

        $a = GeometryModel::distanceValue('location', $loc1->location)->get();
        $this->assertCount(2, $a);
        $this->assertEquals(0, $a[0]->distance);
        $this->assertEquals(1.4142135623, $a[1]->distance); // PHP floats' 11th+ digits don't matter
    }

    public function testDistanceSphereValue()
    {
        $loc1 = new GeometryModel();
        $loc1->location = new Point(40.767864, -73.971732);
        $loc1->save();

        $loc2 = new GeometryModel();
        $loc2->location = new Point(40.767664, -73.971271); // Distance from loc1: 44.741406484236
        $loc2->save();

        $a = GeometryModel::distanceSphereValue('location', $loc1->location)->get();
        $this->assertCount(2, $a);
        $this->assertEquals(0, $a[0]->distance);

        if ($this->after_fix) {
            $this->assertEquals(44.7414064842, $a[1]->distance); // PHP floats' 11th+ digits don't matter
        } else {
            $this->assertEquals(44.7414064845, $a[1]->distance); // PHP floats' 11th+ digits don't matter
        }
    }

    public function testOrderBySpatialWithUnknownFunction()
    {
        $loc = new GeometryModel();
        $loc->location = new Point(1, 1);

        $this->assertException(\Grimzy\LaravelMysqlSpatial\Exceptions\UnknownSpatialFunctionException::class);
        GeometryModel::orderBySpatial('location', $loc->location, 'does-not-exist')->get();
    }

    public function testOrderByDistance()
    {
        $loc2 = new GeometryModel();
        $loc2->location = new Point(2, 2); // Distance from loc1: 1.4142135623731
        $loc2->save();

        $loc1 = new GeometryModel();
        $loc1->location = new Point(1, 1);
        $loc1->save();

        $loc3 = new GeometryModel();
        $loc3->location = new Point(3, 3); // Distance from loc1: 2.8284271247462
        $loc3->save();

        $a = GeometryModel::orderByDistance('location', $loc1->location)->get();
        $this->assertCount(3, $a);
        $this->assertEquals($loc1->location, $a[0]->location);
        $this->assertEquals($loc2->location, $a[1]->location);
        $this->assertEquals($loc3->location, $a[2]->location);

        // Excluding self
        $b = GeometryModel::orderByDistance('location', $loc1->location, 'asc')->get();
        $this->assertCount(3, $b);
        $this->assertEquals($loc1->location, $b[0]->location);
        $this->assertEquals($loc2->location, $b[1]->location);
        $this->assertEquals($loc3->location, $b[2]->location);

        $c = GeometryModel::orderByDistance('location', $loc1->location, 'desc')->get();
        $this->assertCount(3, $c);
        $this->assertEquals($loc3->location, $c[0]->location);
        $this->assertEquals($loc2->location, $c[1]->location);
        $this->assertEquals($loc1->location, $c[2]->location);
    }

    public function testOrderByDistanceSphere()
    {
        $loc2 = new GeometryModel();
        $loc2->location = new Point(40.767664, -73.971271); // Distance from loc1: 44.741406484588
        $loc2->save();

        $loc1 = new GeometryModel();
        $loc1->location = new Point(40.767864, -73.971732);
        $loc1->save();

        $loc3 = new GeometryModel();
        $loc3->location = new Point(40.761434, -73.977619); // Distance from loc1: 870.06424066202
        $loc3->save();

        $a = GeometryModel::orderByDistanceSphere('location', $loc1->location)->get();
        $this->assertCount(3, $a);
        $this->assertEquals($loc1->location, $a[0]->location);
        $this->assertEquals($loc2->location, $a[1]->location);
        $this->assertEquals($loc3->location, $a[2]->location);

        $b = GeometryModel::orderByDistanceSphere('location', $loc1->location, 'asc')->get();
        $this->assertCount(3, $b);
        $this->assertEquals($loc1->location, $b[0]->location);
        $this->assertEquals($loc2->location, $b[1]->location);
        $this->assertEquals($loc3->location, $b[2]->location);

        $c = GeometryModel::orderByDistanceSphere('location', $loc1->location, 'desc')->get();
        $this->assertCount(3, $c);
        $this->assertEquals($loc3->location, $c[0]->location);
        $this->assertEquals($loc2->location, $c[1]->location);
        $this->assertEquals($loc1->location, $c[2]->location);
    }

    //public function testBounding() {
    //    $point = new Point(0, 0);
    //
    //    $linestring1 = \Grimzy\LaravelMysqlSpatial\Types\LineString::fromWkt("LINESTRING(1 1, 2 2)");
    //    $linestring2 = \Grimzy\LaravelMysqlSpatial\Types\LineString::fromWkt("LINESTRING(20 20, 24 24)");
    //    $linestring3 = \Grimzy\LaravelMysqlSpatial\Types\LineString::fromWkt("LINESTRING(0 10, 10 10)");
    //
    //    $geo1 = new GeometryModel();
    //    $geo1->location = $point;
    //    $geo1->line = $linestring1;
    //    $geo1->save();
    //
    //    $geo2 = new GeometryModel();
    //    $geo2->location = $point;
    //    $geo2->line = $linestring2;
    //    $geo2->save();
    //
    //    $geo3 = new GeometryModel();
    //    $geo3->location = $point;
    //    $geo3->line = $linestring3;
    //    $geo3->save();
    //
    //    $polygon = Polygon::fromWKT("POLYGON((0 10,10 10,10 0,0 0,0 10))");
    //
    //    $result = GeometryModel::Bounding($polygon, 'line')->get();
    //    $this->assertCount(2, $result);
    //    $this->assertTrue($result->contains($geo1));
    //    $this->assertFalse($result->contains($geo2));
    //    $this->assertTrue($result->contains($geo3));
    //
    //}
}
