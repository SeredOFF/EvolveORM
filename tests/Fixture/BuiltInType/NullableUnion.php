<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class NullableUnion
{
    public function __construct(
        public int|float|string|bool|array|null $value,
    ) {
    }
}
