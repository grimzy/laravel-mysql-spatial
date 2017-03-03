<?php
namespace Grimzy\LaravelSpatial\Types;

use Countable;
use InvalidArgumentException;

class MultiLineString extends Geometry implements Countable
{
    /**
     * @var LineString[]
     */
    protected $linestrings = [];

    /**
     * @param LineString[] $linestrings
     */
    public function __construct(array $linestrings)
    {
        if (count($linestrings) < 1) {
            throw new InvalidArgumentException('$linestrings must contain at least one entry');
        }

        $validated = array_filter($linestrings, function ($value) {
            return $value instanceof LineString;
        });

        if (count($linestrings) !== count($validated)) {
            throw new InvalidArgumentException('$linestrings must be an array of Points');
        }

        $this->linestrings = $linestrings;
    }

    public function getLineStrings()
    {
        return $this->linestrings;
    }

    public function toWKT()
    {
        return sprintf('MULTILINESTRING(%s)', (string)$this);
    }

    public static function fromString($wktArgument)
    {
        $str = preg_split('/\)\s*,\s*\(/', substr(trim($wktArgument), 1, -1));
        $linestrings = array_map(function ($data) {
            return LineString::fromString($data);
        }, $str);


        return new static($linestrings);
    }

    public function __toString()
    {
        return implode(',', array_map(function (LineString $linestring) {
            return sprintf('(%s)', (string)$linestring);
        }, $this->getLineStrings()));
    }

    public function count()
    {
        return count($this->linestrings);
    }

    /**
     * Convert to GeoJson Point that is jsonable to GeoJSON
     *
     * @return \GeoJson\Geometry\MultiLineString
     */
    public function jsonSerialize()
    {
        $linestrings = [];

        foreach ($this->linestrings as $linestring) {
            $linestrings[] = $linestring->jsonSerialize();
        }

        return new \GeoJson\Geometry\MultiLineString($linestrings);
    }
}
