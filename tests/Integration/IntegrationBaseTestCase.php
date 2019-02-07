<?php

use Grimzy\LaravelMysqlSpatial\SpatialServiceProvider;
use Illuminate\Support\Facades\DB;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class IntegrationBaseTestCase extends BaseTestCase
{
    protected $after_fix = false;
    protected $migrations = [];

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

        /**
         * @param \Illuminate\Config\Repository $config
         */
        $config = $app->config;
        $config->set('database.default', 'mysql');
        $config->set('database.connections.mysql.host', env('DB_HOST', '127.0.0.1'));
        $config->set('database.connections.mysql.database', 'spatial_test');
        $config->set('database.connections.mysql.username', 'root');
        $config->set('database.connections.mysql.password', '');
        $config->set('database.connections.mysql.modes', [
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

        //\DB::listen(function($sql) {
        //    var_dump($sql);
        //});
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
        $migrations = $this->migrations;
        $reverse_sort ? rsort($migrations, SORT_STRING) : sort($migrations, SORT_STRING);

        foreach ($migrations as $migrationClass) {
            $closure($migrationClass);
        }

    }
}
