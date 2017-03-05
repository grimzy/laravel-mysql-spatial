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
        $app['config']->set('database.connections.mysql.host', '172.17.0.2');
        $app['config']->set('database.connections.mysql.database', 'db_test');
        $app['config']->set('database.connections.mysql.username', 'test_user');
        $app['config']->set('database.connections.mysql.password', '123456');

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
        });

        parent::tearDown();
    }

    private function onMigrations(\Closure $closure)
    {
        $fileSystem = new Filesystem();
        $classFinder = new Tools\ClassFinder();

        foreach ($fileSystem->files(__DIR__ . "/Migrations") as $file) {
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
}