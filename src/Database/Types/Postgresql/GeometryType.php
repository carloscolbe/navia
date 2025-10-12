<?php

namespace Navia\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Navia\Database\Types\Type;

class GeometryType extends Type
{
    public const NAME = 'geometry';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'geometry';
    }
}
