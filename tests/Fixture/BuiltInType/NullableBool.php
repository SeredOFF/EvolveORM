<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class NullableBool
{
    public function __construct(
        public ?bool $value,
    ) {
    }
}
