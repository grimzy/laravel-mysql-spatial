<?php
namespace Grimzy\LaravelSpatial\Schema;

class Blueprint extends \Illuminate\Database\Schema\Blueprint
{
    /**
     * Add a geometry column on the table
     *
     * @param   string $column
     * @return \Illuminate\Support\Fluent
     */
    public function geometry($column)
    {
        return $this->addColumn('geometry', $column);
    }

    /**
     * Add a point column on the table
     *
     * @param      $column
     * @return \Illuminate\Support\Fluent
     */
    public function point($column)
    {
        return $this->addColumn('point', $column);
    }

    /**
     * Add a linestring column on the table
     *
     * @param      $column
     * @return \Illuminate\Support\Fluent
     */
    public function linestring($column)
    {
        return $this->addColumn('linestring', $column);
    }

    /**
     * Add a polygon column on the table
     *
     * @param      $column
     * @return \Illuminate\Support\Fluent
     */
    public function polygon($column)
    {
        return $this->addColumn('polygon', $column);
    }

    /**
     * Add a multipoint column on the table
     *
     * @param      $column
     * @return \Illuminate\Support\Fluent
     */
    public function multipoint($column)
    {
        return $this->addColumn('multipoint', $column);
    }

    /**
     * Add a multilinestring column on the table
     *
     * @param      $column
     * @return \Illuminate\Support\Fluent
     */
    public function multilinestring($column)
    {
        return $this->addColumn('multilinestring', $column);
    }

    /**
     * Add a multipolygon column on the table
     *
     * @param      $column
     * @return \Illuminate\Support\Fluent
     */
    public function multipolygon($column)
    {
        return $this->addColumn('multipolygon', $column);
    }

    /**
     * Add a geometrycollection column on the table
     *
     * @param      $column
     * @return \Illuminate\Support\Fluent
     */
    public function geometrycollection($column)
    {
        return $this->addColumn('geometrycollection', $column);
    }
}
