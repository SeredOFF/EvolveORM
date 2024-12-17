<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

enum StringEnum: string
{
    case One = 'one';
    case Two = 'two';
}
