<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class StrictFloat
{
    public function __construct(
        public float $value,
    ) {
    }
}
