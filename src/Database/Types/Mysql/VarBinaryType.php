<?php

namespace Navia\Database\Types\Mysql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Navia\Database\Types\Type;

class VarBinaryType extends Type
{
    public const NAME = 'varbinary';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        $field['length'] = empty($field['length']) ? 255 : $field['length'];

        return "varbinary({$field['length']})";
    }
}
