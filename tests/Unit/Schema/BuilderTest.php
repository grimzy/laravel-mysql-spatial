<?php
namespace Schema;

use BaseTestCase;
use Grimzy\LaravelSpatial\MysqlConnection;
use Grimzy\LaravelSpatial\Schema\Builder;
use Grimzy\LaravelSpatial\Schema\Blueprint;
use Mockery;

class BuilderTest extends BaseTestCase
{
    public function testReturnsCorrectBlueprint()
    {
        $connection = Mockery::mock(MysqlConnection::class);
        $connection->shouldReceive('getSchemaGrammar')->once()->andReturn(null);

        $mock = Mockery::mock(Builder::class, [$connection]);
        $mock->makePartial()->shouldAllowMockingProtectedMethods();
        $blueprint = $mock->createBlueprint('test', function () {
        });

        $this->assertInstanceOf(Blueprint::class, $blueprint);
    }
}
