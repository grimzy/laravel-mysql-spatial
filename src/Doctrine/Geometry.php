<?php

namespace Grimzy\LaravelMysqlSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Geometry extends Type
{
    const GEOMETRY = 'geometry';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'geometry';
    }

    public function getName(): string
    {
        return self::GEOMETRY;
    }
}
