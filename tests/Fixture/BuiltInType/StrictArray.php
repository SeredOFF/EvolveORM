<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class StrictArray
{
    public function __construct(
        public array $value,
    ) {
    }
}
