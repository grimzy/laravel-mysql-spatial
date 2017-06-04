<?php

namespace Grimzy\LaravelMysqlSpatial;

use Grimzy\LaravelMysqlSpatial\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\DatabaseServiceProvider;

/**
 * Class DatabaseServiceProvider
 *
 * @package Grimzy\LaravelMysqlSpatial
 */
class SpatialServiceProvider extends DatabaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });

        if(class_exists('Doctrine\DBAL\Types\Type')) {
            // Prevent geometry type fields from throwing a 'type not found' error when changing them
            $geometries = [
                'geometry' => "Grimzy\LaravelMysqlSpatial\Doctrine\Geometry",
                'point' => "Grimzy\LaravelMysqlSpatial\Doctrine\Point",
                'linestring' => "Grimzy\LaravelMysqlSpatial\Doctrine\LineString",
                'polygon' => "Grimzy\LaravelMysqlSpatial\Doctrine\Polygon",
                'multipoint' => "Grimzy\LaravelMysqlSpatial\Doctrine\MultiPoint",
                'multilinestring' => "Grimzy\LaravelMysqlSpatial\Doctrine\MultiLineString",
                'multipolygon' => "Grimzy\LaravelMysqlSpatial\Doctrine\MultiPolygon",
                'geometrycollection' => "Grimzy\LaravelMysqlSpatial\Doctrine\GeometryCollection"
            ];
            $typeNames = array_keys(\Doctrine\DBAL\Types\Type::getTypesMap());
            foreach($geometries as $type => $class) {
                if(!in_array($type, $typeNames)) {
                    \Doctrine\DBAL\Types\Type::addType($type, $class);
                }
            }
        }

    }
}