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
    public function setUp(): void
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

    public function tearDown(): void
    {
        $this->onMigrations(function ($migrationClass) {
            (new $migrationClass())->down();
        }, true);

        parent::tearDown();
    }

    // MySQL 8.0.4 fixed bug #26941370 and bug #88031
    private function isMySQL8AfterFix()
    {
        $expression = DB::raw('select version()');
        $results = DB::select($expression->getValue(DB::connection()->getQueryGrammar()));
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

    protected function assertException($exceptionName, $exceptionMessage = null)
    {
        if (method_exists(parent::class, 'expectException')) {
            parent::expectException($exceptionName);
            if (!is_null($exceptionMessage)) {
                $this->expectExceptionMessage($exceptionMessage);
            }
        } else {
            $this->setExpectedException($exceptionName, $exceptionMessage);
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
