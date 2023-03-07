<?php

namespace Grimzy\LaravelMysqlSpatial\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MultiLineString extends Type
{
    const MULTILINESTRING = 'multilinestring';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'multilinestring';
    }

    public function getName(): string
    {
        return self::MULTILINESTRING;
    }
}
