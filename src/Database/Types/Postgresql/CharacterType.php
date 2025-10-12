<?php

namespace Navia\Database\Types\Postgresql;

use Navia\Database\Types\Common\CharType;

class CharacterType extends CharType
{
    public const NAME = 'character';
    public const DBTYPE = 'bpchar';
}
