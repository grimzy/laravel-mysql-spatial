<?php

namespace Grimzy\LaravelMysqlSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Polygon extends Type
{
    const POLYGON = 'polygon';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'polygon';
    }

    public function getName(): string
    {
        return self::POLYGON;
    }
}
