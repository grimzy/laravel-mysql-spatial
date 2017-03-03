<?php
namespace Grimzy\LaravelSpatial\Schema;

use Closure;
use Illuminate\Database\Schema\MysqlBuilder;

class Builder extends MysqlBuilder
{
    /**
     * Create a new command set with a Closure.
     *
     * @param string $table
     * @param Closure $callback
     * @return Blueprint
     */
    protected function createBlueprint($table, Closure $callback = null)
    {
        return new Blueprint($table, $callback);
    }
}
