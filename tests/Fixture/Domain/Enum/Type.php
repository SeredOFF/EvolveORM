<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\Enum;

enum Type: string
{
    case Large = 'large';
    case Medium = 'medium';
    case Small = 'small';
}
