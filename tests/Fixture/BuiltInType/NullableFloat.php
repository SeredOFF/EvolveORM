<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class NullableFloat
{
    public function __construct(
        public ?float $value,
    ) {
    }
}
