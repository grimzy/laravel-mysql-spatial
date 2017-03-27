<?php
use Grimzy\LaravelSpatial\SpatialServiceProvider;
use Grimzy\LaravelSpatial\Types\Point;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\TestCase;

class SpatialTest extends TestCase
{
    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';
        $app->register(SpatialServiceProvider::class);

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $app['config']->set('database.connections.mysql.database', 'test');
        $app['config']->set('database.connections.mysql.username', 'root');
        $app['config']->set('database.connections.mysql.password', '');

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

        $this->onMigrations(function ($migrationClass) {
            (new $migrationClass)->up();
        });
    }

    public function tearDown()
    {
        $this->onMigrations(function ($migrationClass) {
            (new $migrationClass)->down();
        }, true);

        parent::tearDown();
    }

    protected function assertDatabaseHas($table, array $data, $connection = null)
    {
        if (method_exists($this, 'seeInDatabase')) {
            $this->seeInDatabase($table, $data, $connection);
        } else {
            parent::assertDatabaseHas($table, $data, $connection);
        }
    }

    private function onMigrations(\Closure $closure, $reverse_sort = false)
    {
        $fileSystem = new Filesystem();
        $classFinder = new Tools\ClassFinder();

        $migrations = $fileSystem->files(__DIR__ . "/Migrations");
        $reverse_sort ? rsort($migrations, SORT_STRING) : sort($migrations, SORT_STRING);

        foreach ($migrations as $file) {
            $fileSystem->requireOnce($file);
            $migrationClass = $classFinder->findClass($file);

            $closure($migrationClass);
        }
    }

    // TODO: Test with a model missing $spatialFields to expect SpatialFieldNotDefinedException

    public function testInsert()
    {
        $geo = new GeometryModel();
        $geo->location = new Point(1, 2);
        $geo->save();
        $this->assertDatabaseHas('geometry', ['id' => $geo->id]);
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
        $loc1->location = new Point(40.767864, -73.971732);
        $loc1->save();

        $loc2 = new GeometryModel();
        $loc2->location = new Point(40.767664, -73.971271); // Distance from loc1: 44.7414064845878
        $loc2->save();

        $loc3 = new GeometryModel();
        $loc3->location = new Point(40.761434, -73.977619);
        $loc3->save();

        $a = GeometryModel::distance(45, $loc1->location, 'location')->get();
        $this->assertCount(2, $a);
        $this->assertTrue($a->contains($loc1));
        $this->assertTrue($a->contains($loc2));
        $this->assertFalse($a->contains($loc3));

        $b = GeometryModel::distance(45, $loc1->location, 'location', true)->get();
        $this->assertCount(1, $b);
        $this->assertFalse($b->contains($loc1));
        $this->assertTrue($b->contains($loc2));
        $this->assertFalse($b->contains($loc3));

        $c = GeometryModel::distance(44.741406484587, $loc1->location, 'location')->get();
        $this->assertCount(1, $c);
        $this->assertTrue($c->contains($loc1));
        $this->assertFalse($c->contains($loc2));
        $this->assertFalse($c->contains($loc3));
    }
}