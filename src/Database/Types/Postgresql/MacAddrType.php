<?php

namespace Navia\Database\Types\Postgresql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Navia\Database\Types\Type;

class MacAddrType extends Type
{
    public const NAME = 'macaddr';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'macaddr';
    }
}
