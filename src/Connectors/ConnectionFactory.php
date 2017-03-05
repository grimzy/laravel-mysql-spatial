<?php
namespace Grimzy\LaravelSpatial\Connectors;

use Grimzy\LaravelSpatial\MysqlConnection;
use PDO;

class ConnectionFactory extends \Illuminate\Database\Connectors\ConnectionFactory
{
    /**
     * @param string $driver
     * @param \Closure|PDO $connection
     * @param string $database
     * @param string $prefix
     * @param array $config
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($this->container->bound($key = "db.connection.{$driver}")) {
            return $this->container->make($key, [$connection, $database, $prefix, $config]);
        }

        if ($driver === 'mysql') {
            return new MysqlConnection($connection, $database, $prefix, $config);
        }

        return parent::createConnection($driver, $connection, $database, $prefix, $config);
    }
}
