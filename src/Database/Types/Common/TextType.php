<?php

namespace Navia\Database\Types\Common;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Navia\Database\Types\Type;

class TextType extends Type
{
    public const NAME = 'text';

    public function getSQLDeclaration(array $field, AbstractPlatform $platform)
    {
        return 'text';
    }
}
