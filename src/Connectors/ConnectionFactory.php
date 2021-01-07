<?php

namespace Grimzy\LaravelMysqlSpatial\Connectors;

use Grimzy\LaravelMysqlSpatial\MysqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\ConnectionFactory as IlluminateConnectionFactory;
use PDO;

class ConnectionFactory extends IlluminateConnectionFactory
{
    /**
     * @param string       $driver
     * @param \Closure|PDO $connection
     * @param string       $database
     * @param string       $prefix
     * @param array        $config
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);    // @codeCoverageIgnore
        }

        if ($driver === 'mysql') {
            return new MysqlConnection($connection, $database, $prefix, $config);
        }

        return parent::createConnection($driver, $connection, $database, $prefix, $config);
    }
}
