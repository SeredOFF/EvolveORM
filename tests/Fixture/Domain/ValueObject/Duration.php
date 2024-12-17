<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

final readonly class Duration
{
    public function __construct(
        public int $seconds,
    ) {
    }
}
