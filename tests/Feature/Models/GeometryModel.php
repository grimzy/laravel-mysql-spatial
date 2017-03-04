<?php
namespace Grimzy\LaravelSpatial\Tests\Feature\Models;

use Grimzy\LaravelSpatial\Eloquent\SpatialTrait;
use Illuminate\Database\Eloquent\Model;

class GeometryModel extends Model
{
    use SpatialTrait;

    protected $table = 'geometry';

    protected $spatialFields = ['location'];
}