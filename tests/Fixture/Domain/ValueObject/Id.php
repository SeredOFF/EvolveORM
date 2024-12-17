<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

final readonly class Id
{
    public function __construct(
        public string $value,
    ) {
    }
}
