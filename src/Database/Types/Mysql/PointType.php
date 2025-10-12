<?php

namespace Navia\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Navia\Database\Types\Type;

class PointType extends Type
{
    public const NAME = 'point';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'point';
    }
}
