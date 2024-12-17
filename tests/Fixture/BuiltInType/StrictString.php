<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class StrictString
{
    public function __construct(
        public string $value,
    ) {
    }
}
