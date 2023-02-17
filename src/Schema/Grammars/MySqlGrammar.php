<?php

namespace Grimzy\LaravelMysqlSpatial\Schema\Grammars;

use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar as IlluminateMySqlGrammar;
use Illuminate\Support\Fluent;

class MySqlGrammar extends IlluminateMySqlGrammar
{
    const COLUMN_MODIFIER_SRID = 'Srid';

    public function __construct()
    {
        // Enable SRID as a column modifier
        if (! in_array(self::COLUMN_MODIFIER_SRID, $this->modifiers)) {
            $this->modifiers[] = self::COLUMN_MODIFIER_SRID;
        }
    }

    /**
     * Adds a statement to add a geometry column.
     *
     *
     * @return string
     */
    public function typeGeometry(Fluent $column)
    {
        return 'GEOMETRY';
    }

    /**
     * Adds a statement to add a point column.
     *
     *
     * @return string
     */
    public function typePoint(Fluent $column)
    {
        return 'POINT';
    }

    /**
     * Adds a statement to add a linestring column.
     *
     *
     * @return string
     */
    public function typeLinestring(Fluent $column)
    {
        return 'LINESTRING';
    }

    /**
     * Adds a statement to add a polygon column.
     *
     *
     * @return string
     */
    public function typePolygon(Fluent $column)
    {
        return 'POLYGON';
    }

    /**
     * Adds a statement to add a multipoint column.
     *
     *
     * @return string
     */
    public function typeMultipoint(Fluent $column)
    {
        return 'MULTIPOINT';
    }

    /**
     * Adds a statement to add a multilinestring column.
     *
     *
     * @return string
     */
    public function typeMultilinestring(Fluent $column)
    {
        return 'MULTILINESTRING';
    }

    /**
     * Adds a statement to add a multipolygon column.
     *
     *
     * @return string
     */
    public function typeMultipolygon(Fluent $column)
    {
        return 'MULTIPOLYGON';
    }

    /**
     * Adds a statement to add a geometrycollection column.
     *
     *
     * @return string
     */
    public function typeGeometrycollection(Fluent $column)
    {
        return 'GEOMETRYCOLLECTION';
    }

    /**
     * Compile a spatial index key command.
     *
     *
     * @return string
     */
    public function compileSpatial(Blueprint $blueprint, Fluent $command)
    {
        return $this->compileKey($blueprint, $command, 'spatial');
    }

    /**
     * Get the SQL for a SRID column modifier.
     *
     *
     * @return string|null
     */
    protected function modifySrid(\Illuminate\Database\Schema\Blueprint $blueprint, Fluent $column)
    {
        if (! is_null($column->srid) && is_int($column->srid) && $column->srid > 0) {
            return ' srid '.$column->srid;
        }
    }
}
