<?php

namespace Grimzy\LaravelMysqlSpatial\Tests;

use Grimzy\LaravelMysqlSpatial\LaravelMysqlSpatialServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Grimzy\\LaravelMysqlSpatial\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelMysqlSpatialServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-mysql-spatial_table.php.stub';
        $migration->up();
        */
    }
}
