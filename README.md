# Laravel MySQL Spatial extension

[![Build Status](https://img.shields.io/travis/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://travis-ci.org/grimzy/laravel-mysql-spatial)
[![Code Climate](https://img.shields.io/codeclimate/maintainability/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://codeclimate.com/github/grimzy/laravel-mysql-spatial/maintainability)
[![Code Climate](https://img.shields.io/codeclimate/c/grimzy/laravel-mysql-spatial.svg?style=flat-square&colorB=4BCA2A)](https://codeclimate.com/github/grimzy/laravel-mysql-spatial/test_coverage)[![Packagist](https://img.shields.io/packagist/v/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://packagist.org/packages/grimzy/laravel-mysql-spatial)
[![Packagist](https://img.shields.io/packagist/dt/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://packagist.org/packages/grimzy/laravel-mysql-spatial)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](LICENSE)

Laravel package to easily work with [MySQL Spatial Data Types](https://dev.mysql.com/doc/refman/5.7/en/spatial-datatypes.html) and [MySQL Spatial Functions](https://dev.mysql.com/doc/refman/5.7/en/spatial-function-reference.html).

Please check the documentation for your MySQL version. MySQL's Extension for Spatial Data was added in MySQL 5.5 but many Spatial Functions were changed in 5.6 and 5.7.

**Versions**

- `1.x.x`: MySQL 5.6 (also supports MySQL 5.5 but not all spatial analysis functions)
- `2.x.x`: MySQL 5.7 and 8.0

## Installation

Add the package using composer:

```shell
composer require grimzy/laravel-mysql-spatial
```

For MySQL 5.6 and 5.5:

```shell
composer require grimzy/laravel-mysql-spatial:^1.0
```

For Laravel versions before 5.5 or if not using auto-discovery, register the service provider in `config/app.php`:

```php
'providers' => [
  /*
   * Package Service Providers...
   */
  Grimzy\LaravelMysqlSpatial\SpatialServiceProvider::class,
],
```

## Quickstart

### Create a migration

From the command line:

```shell
php artisan make:migration create_places_table
```

Then edit the migration you just created by adding at least one spatial data field:

```php
use Illuminate\Database\Migrations\Migration;
use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;

class CreatePlacesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name')->unique();
            // Add a Point spatial data field named location
            $table->point('location')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('places');
    }
}
```

Run the migration:

```shell
php artisan migrate
```

### Create a model

From the command line:

```shell
php artisan make:model Place
```

Then edit the model you just created. It must use the `SpatialTrait` and define an array called `$spatialFields` with the name of the MySQL Spatial Data field(s) created in the migration:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

/**
 * @property \Grimzy\LaravelMysqlSpatial\Types\Point $location
 */
class Place extends Model
{
    use SpatialTrait;

    protected $fillable = [
        'name',
    ];

    protected $spatialFields = [
        'location',
    ];
}
```

### Saving a model

```php
$place1 = new Place();
$place1->name = 'Empire State Building';
$place1->location = new Point(40.7484404, -73.9878441);
$place1->save();
```

### Retrieving a model

```php
$place2 = Place::first();
$lat = $place2->location->getLat();	// 40.7484404
$lng = $place2->location->getLng();	// -73.9878441
```

## Migration

Available [MySQL Spatial Types](https://dev.mysql.com/doc/refman/5.7/en/spatial-datatypes.html) migration blueprints:

- geometry
- point
- lineString
- polygon
- multiPoint
- multiLineString
- multiPolygon
- geometryCollection

### Spatial index

You can add or drop spatial indexes in your migrations with the `spatialIndex` and `dropSpatialIndex` blueprints.

Note about spatial indexes from the [MySQL documentation](https://dev.mysql.com/doc/refman/5.7/en/creating-spatial-indexes.html):

> For [`MyISAM`](https://dev.mysql.com/doc/refman/5.7/en/myisam-storage-engine.html) and (as of MySQL 5.7.5) `InnoDB` tables, MySQL can create spatial indexes using syntax similar to that for creating regular indexes, but using the `SPATIAL` keyword. Columns in spatial indexes must be declared `NOT NULL`.

From the command line:

```shell
php artisan make:migration update_places_table
```

Then edit the migration you just created:

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // MySQL < 5.7.5: table has to be MyISAM
        // \DB::statement('ALTER TABLE places ENGINE = MyISAM');

        Schema::table('places', function (Blueprint $table) {
            // Make sure point is not nullable
            $table->point('location')->change();
          
            // Add a spatial index on the location field
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('places', function (Blueprint $table) {
            $table->dropSpatialIndex(['location']); // either an array of column names or the index name
        });

        // \DB::statement('ALTER TABLE places ENGINE = InnoDB');

        Schema::table('places', function (Blueprint $table) {
            $table->point('location')->nullable()->change();
        });
    }
}
```
## Models

Available geometry classes:

- `Point($lat, $lng)`
- `MultiPoint(Point[])`
- `LineString(Point[])`
- `MultiLineString(LineString[])`
- `Polygon(LineString[])`
- `MultiPolygon(Polygon[])`
- `GeometryCollection(Geometry[])` *(a collection of spatial models)*

## Scopes: Spatial analysis functions

Spatial analysis functions are implemented using [Eloquent Local Scopes](https://laravel.com/docs/5.4/eloquent#local-scopes).

Available scopes:

- `distance($geometryColumn, $geometry, $distance)`
- `distanceExcludingSelf($geometryColumn, $geometry, $distance)`
- `distanceSphere($geometryColumn, $geometry, $distance)`
- `distanceSphereExcludingSelf($geometryColumn, $geometry, $distance)`
- `comparison($geometryColumn, $geometry, $relationship)`
- `within($geometryColumn, $polygon)`
- `crosses($geometryColumn, $geometry)`
- `contains($geometryColumn, $geometry)`
- `disjoint($geometryColumn, $geometry)`
- `equals($geometryColumn, $geometry)`
- `intersects($geometryColumn, $geometry)`
- `overlaps($geometryColumn, $geometry)`
- `doesTouch($geometryColumn, $geometry)`

*Note that behavior and availability of MySQL spatial analysis functions differs in each MySQL version (cf. [documentation](https://dev.mysql.com/doc/refman/5.7/en/spatial-function-reference.html)).*

## Credits

Originally inspired from [njbarrett's Laravel postgis package](https://github.com/njbarrett/laravel-postgis).