<?php

namespace Grimzy\LaravelMysqlSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class Point extends Type
{
    const POINT = 'point';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'point';
    }

    public function getName(): string
    {
        return self::POINT;
    }
}
