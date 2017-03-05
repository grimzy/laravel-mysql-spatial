<?php

use Illuminate\Container\Container;
use Grimzy\LaravelSpatial\Connectors\ConnectionFactory;
use Grimzy\LaravelSpatial\MysqlConnection;
use Stubs\PDOStub;

class ConnectionFactoryBaseTest extends BaseTestCase
{
    public function testMakeCallsCreateConnection()
    {
        $mysql_config = ['driver' => 'mysql', 'prefix' => 'prefix', 'database' => 'database', 'name' => 'foo'];
        $pdo = new PDOStub();

        $factory = Mockery::mock(ConnectionFactory::class, [new Container()])->makePartial();
        $factory->shouldAllowMockingProtectedMethods();
        $conn = $factory->createConnection('mysql', $pdo, 'database', 'prefix', $mysql_config);

        $this->assertInstanceOf(MysqlConnection::class, $conn);
    }
}
