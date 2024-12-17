<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class StrictBool
{
    public function __construct(
        public bool $value,
    ) {
    }
}
