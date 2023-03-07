<?php

namespace Grimzy\LaravelMysqlSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class GeometryCollection extends Type
{
    const GEOMETRYCOLLECTION = 'geometrycollection';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'geometrycollection';
    }

    public function getName(): string
    {
        return self::GEOMETRYCOLLECTION;
    }
}
