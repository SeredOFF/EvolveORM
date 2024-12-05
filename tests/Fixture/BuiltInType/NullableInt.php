<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class NullableInt
{
    public function __construct(
        public ?int $value,
    ) {
    }
}
