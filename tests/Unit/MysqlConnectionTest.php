<?php

namespace Grimzy\LaravelMysqlSpatial\Tests\Unit;

use Grimzy\LaravelMysqlSpatial\MysqlConnection;
use Grimzy\LaravelMysqlSpatial\Schema\Builder;
use PHPUnit\Framework\TestCase;

class MysqlConnectionTest extends TestCase
{
    private $mysqlConnection;

    protected function setUp(): void
    {
        $mysqlConfig = ['driver' => 'mysql', 'prefix' => 'prefix', 'database' => 'database', 'name' => 'foo'];
        $this->mysqlConnection = new MysqlConnection($this->createMock(\PDO::class), 'database', 'prefix', $mysqlConfig);
    }

    public function testGetSchemaBuilder()
    {
        $builder = $this->mysqlConnection->getSchemaBuilder();

        $this->assertInstanceOf(Builder::class, $builder);
    }
}
