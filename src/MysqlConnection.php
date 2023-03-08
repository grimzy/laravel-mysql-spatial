<?php

namespace Grimzy\LaravelMysqlSpatial;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Grimzy\LaravelMysqlSpatial\Schema\Builder;
use Grimzy\LaravelMysqlSpatial\Schema\Grammars\MySqlGrammar;
use Illuminate\Database\Grammar;
use Illuminate\Database\MySqlConnection as IlluminateMySqlConnection;
use Illuminate\Database\Schema\MySqlBuilder;

class MysqlConnection extends IlluminateMySqlConnection
{
    /**
     * @param $pdo
     * @param string $database
     * @param string $tablePrefix
     * @param array $config
     * @throws Exception
     */
    public function __construct(
        $pdo,
        string $database = '',
        string $tablePrefix = '',
        array $config = []
    )
    {
        parent::__construct($pdo, $database, $tablePrefix, $config);

        if (class_exists(DoctrineType::class)) {
            // Prevent geometry type fields from throwing a 'type not found' error when changing them
            $geometries = [
                'geometry',
                'point',
                'linestring',
                'polygon',
                'multipoint',
                'multilinestring',
                'multipolygon',
                'geometrycollection',
                'geomcollection',
            ];
            $dbPlatform = $this->getDoctrineSchemaManager()->getDatabasePlatform();
            foreach ($geometries as $type) {
                $dbPlatform->registerDoctrineTypeMapping($type, 'string');
            }
        }
    }

    /**
     * Get the default schema grammar instance.
     *
     * @return Grammar
     */
    protected function getDefaultSchemaGrammar(): Grammar
    {
        return $this->withTablePrefix(new MySqlGrammar());
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return MySqlBuilder
     */
    public function getSchemaBuilder(): MySqlBuilder|Builder
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new Builder($this);
    }
}
