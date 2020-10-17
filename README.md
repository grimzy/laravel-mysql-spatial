# Laravel MySQL Spatial extension

[![Build Status](https://img.shields.io/travis/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://travis-ci.org/grimzy/laravel-mysql-spatial)
[![Code Climate](https://img.shields.io/codeclimate/maintainability/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://codeclimate.com/github/grimzy/laravel-mysql-spatial/maintainability)
[![Code Climate](https://img.shields.io/codeclimate/c/grimzy/laravel-mysql-spatial.svg?style=flat-square&colorB=4BCA2A)](https://codeclimate.com/github/grimzy/laravel-mysql-spatial/test_coverage) [![Packagist](https://img.shields.io/packagist/v/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://packagist.org/packages/grimzy/laravel-mysql-spatial)
[![Packagist](https://img.shields.io/packagist/dt/grimzy/laravel-mysql-spatial.svg?style=flat-square)](https://packagist.org/packages/grimzy/laravel-mysql-spatial) [![StyleCI](https://github.styleci.io/repos/83766141/shield?branch=master)](https://github.styleci.io/repos/83766141) 
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=flat-square)](LICENSE)

Laravel package to easily work with [MySQL Spatial Data Types](https://dev.mysql.com/doc/refman/8.0/en/spatial-type-overview.html) and [MySQL Spatial Functions](https://dev.mysql.com/doc/refman/8.0/en/spatial-function-reference.html).

Please check the documentation for your MySQL version. MySQL's Extension for Spatial Data was added in MySQL 5.5 but many Spatial Functions were changed in 5.6 and 5.7.

**Versions**

- `1.x.x`: MySQL 5.6 (also supports MySQL 5.5 but not all spatial analysis functions)
- `2.x.x`: MySQL 5.7 and 8.0 (Laravel version < 8.0)
- `3.x.x`: MySQL 8.0 with SRID support (Laravel version < 8.0)
- `4.x.x`: MySQL 8.0 with SRID support (Laravel 8)
- **`5.x.x`: MySQL 5.7 and 8.0 (Laravel 8) [Current branch]**

This package also works with MariaDB. Please refer to the [MySQL/MariaDB Spatial Support Matrix](https://mariadb.com/kb/en/library/mysqlmariadb-spatial-support-matrix/) for compatibility.

## Installation

Add the package using composer:

```shell
$ composer require grimzy/laravel-mysql-spatial:^5.0

# or for Laravel version < 8.0
$ composer require grimzy/laravel-mysql-spatial:^2.0
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

Then edit the migration you just created by adding at least one spatial data field. For Laravel versions prior to 5.5, you can use the Blueprint provided by this package (Grimzy\LaravelMysqlSpatial\Schema\Blueprint):

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

// For Laravel < 5.5
// use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;

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
            // Add a Polygon spatial data field named area
            $table->polygon('area')->nullable();
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
 * @property \Grimzy\LaravelMysqlSpatial\Types\Point   $location
 * @property \Grimzy\LaravelMysqlSpatial\Types\Polygon $area
 */
class Place extends Model
{
    use SpatialTrait;

    protected $fillable = [
        'name'
    ];

    protected $spatialFields = [
        'location',
        'area'
    ];
}
```

### Saving a model

```php
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Grimzy\LaravelMysqlSpatial\Types\LineString;

$place1 = new Place();
$place1->name = 'Empire State Building';

// saving a point
$place1->location = new Point(40.7484404, -73.9878441);	// (lat, lng)
$place1->save();

// saving a polygon
$place1->area = new Polygon([new LineString([
    new Point(40.74894149554006, -73.98615270853043),
    new Point(40.74848633046773, -73.98648262023926),
    new Point(40.747925497790725, -73.9851602911949),
    new Point(40.74837050671544, -73.98482501506805),
    new Point(40.74894149554006, -73.98615270853043)
])]);
$place1->save();

$place1->area = new Polygon();

```

### Retrieving a model

```php
$place2 = Place::first();
$lat = $place2->location->getLat();	// 40.7484404
$lng = $place2->location->getLng();	// -73.9878441
```

## Geometry classes

### Available Geometry classes

| Grimzy\LaravelMysqlSpatial\Types                             | OpenGIS Class                                                |
| ------------------------------------------------------------ | ------------------------------------------------------------ |
| `Point($lat, $lng)`                                          | [Point](https://dev.mysql.com/doc/refman/8.0/en/gis-class-point.html) |
| `MultiPoint(Point[])`                                        | [MultiPoint](https://dev.mysql.com/doc/refman/8.0/en/gis-class-multipoint.html) |
| `LineString(Point[])`                                        | [LineString](https://dev.mysql.com/doc/refman/8.0/en/gis-class-linestring.html) |
| `MultiLineString(LineString[])`                              | [MultiLineString](https://dev.mysql.com/doc/refman/8.0/en/gis-class-multilinestring.html) |
| `Polygon(LineString[])` *([exterior and interior boundaries](https://dev.mysql.com/doc/refman/8.0/en/gis-class-polygon.html))* | [Polygon](https://dev.mysql.com/doc/refman/8.0/en/gis-class-polygon.html) |
| `MultiPolygon(Polygon[])`                                    | [MultiPolygon](https://dev.mysql.com/doc/refman/8.0/en/gis-class-multipolygon.html) |
| `GeometryCollection(Geometry[])`                             | [GeometryCollection](https://dev.mysql.com/doc/refman/8.0/en/gis-class-geometrycollection.html) |

Check out the [Class diagram](https://user-images.githubusercontent.com/1837678/30788608-a5afd894-a16c-11e7-9a51-0a08b331d4c4.png).

### Using Geometry classes

In order for your Eloquent Model to handle the Geometry classes, it must use the `Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait` trait and define a `protected` property `$spatialFields`  as an array of MySQL Spatial Data Type column names (example in [Quickstart](#user-content-create-a-model)).

#### IteratorAggregate and ArrayAccess

The "composite" Geometries (`LineString`, `Polygon`, `MultiPoint`, `MultiLineString`, and `GeometryCollection`) implement [`IteratorAggregate`](http://php.net/manual/en/class.iteratoraggregate.php) and [`ArrayAccess`](http://php.net/manual/en/class.arrayaccess.php); making it easy to perform Iterator and Array operations. For example:

```php
$polygon = $multipolygon[10];	// ArrayAccess

// IteratorAggregate
for($polygon as $i => $linestring) {
  echo (string) $linestring;
}

```

#### Helpers

##### From/To Well Known Text ([WKT](https://dev.mysql.com/doc/refman/5.7/en/gis-data-formats.html#gis-wkt-format))

```php
// fromWKT($wkt)
$point = Point::fromWKT('POINT(2 1)');
$point->toWKT();	// POINT(2 1)

$polygon = Polygon::fromWKT('POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))');
$polygon->toWKT();	// POLYGON((0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1))
```

##### From/To String

```php
// fromString($wkt)
$point = new Point(1, 2);	// lat, lng
(string)$point				// lng, lat: 2 1

$polygon = Polygon::fromString('(0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1)');
(string)$polygon;	// (0 0,4 0,4 4,0 4,0 0),(1 1, 2 1, 2 2, 1 2,1 1)
```

##### From/To JSON ([GeoJSON](http://geojson.org/))

The Geometry classes implement [`JsonSerializable`](http://php.net/manual/en/class.jsonserializable.php) and `Illuminate\Contracts\Support\Jsonable` to help serialize into GeoJSON:

```php
$point = new Point(40.7484404, -73.9878441);

json_encode($point); // or $point->toJson();

// {
//   "type": "Feature",
//   "properties": {},
//   "geometry": {
//     "type": "Point",
//     "coordinates": [
//       -73.9878441,
//       40.7484404
//     ]
//   }
// }
```

To deserialize a GeoJSON string into a Geometry class, you can use `Geometry::fromJson($json_string)` :

```php
$location = Geometry::fromJson('{"type":"Point","coordinates":[3.4,1.2]}');
$location instanceof Point::class;	// true
$location->getLat();	// 1.2
$location->getLng()); 	// 3.4
```

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
- `orderBySpatial($geometryColumn, $geometry, $orderFunction, $direction = 'asc')`
- `orderByDistance($geometryColumn, ​$geometry, ​$direction = 'asc')`
- `orderByDistanceSphere($geometryColumn, ​$geometry, ​$direction = 'asc')`

*Note that behavior and availability of MySQL spatial analysis functions differs in each MySQL version (cf. [documentation](https://dev.mysql.com/doc/refman/5.7/en/spatial-function-reference.html)).*

## Migrations

For Laravel versions prior to 5.5, you can use the Blueprint provided with this package: `Grimzy\LaravelMysqlSpatial\Schema\Blueprint`.

```php
use Illuminate\Database\Migrations\Migration;
use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;

class CreatePlacesTable extends Migration {
    // ...
}
```

### Columns

Available [MySQL Spatial Types](https://dev.mysql.com/doc/refman/5.7/en/spatial-datatypes.html) migration blueprints:

- `$table->geometry('column_name')`
- `$table->point('column_name')`
- `$table->lineString('column_name')`
- `$table->polygon('column_name')`
- `$table->multiPoint('column_name')`
- `$table->multiLineString('column_name')`
- `$table->multiPolygon('column_name')`
- `$table->geometryCollection('column_name')`

### Spatial indexes

You can add or drop spatial indexes in your migrations with the `spatialIndex` and `dropSpatialIndex` blueprints.

- `$table->spatialIndex('column_name')`
- `$table->dropSpatialIndex(['column_name'])` or `$table->dropSpatialIndex('index_name')`

Note about spatial indexes from the [MySQL documentation](https://dev.mysql.com/doc/refman/5.7/en/creating-spatial-indexes.html):

> For [`MyISAM`](https://dev.mysql.com/doc/refman/5.7/en/myisam-storage-engine.html) and (as of MySQL 5.7.5) `InnoDB` tables, MySQL can create spatial indexes using syntax similar to that for creating regular indexes, but using the `SPATIAL` keyword. Columns in spatial indexes must be declared `NOT NULL`.

Also please read this [**important note**](https://laravel.com/docs/5.5/migrations#indexes) regarding Index Lengths in the Laravel 5.6 documentation.

For example, as a follow up to the [Quickstart](#user-content-create-a-migration); from the command line, generate a new migration:

```shell
php artisan make:migration update_places_table
```

Then edit the migration file that you just created:

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

## Tests

```shell
composer test
# or 
composer test:unit
composer test:integration
```

Integration tests require a running MySQL database. If you have Docker installed, you can start easily start one:

```shell
make start_db 		# starts MySQL 8.0
# or
make start_db V=5.7 # starts a MySQL 5.7
```

## Contributing

Recommendations and pull request are most welcome! Pull requests with tests are the best! There are still a lot of MySQL spatial functions to implement or creative ways to use spatial functions. 

## Credits

Originally inspired from [njbarrett's Laravel postgis package](https://github.com/njbarrett/laravel-postgis).

