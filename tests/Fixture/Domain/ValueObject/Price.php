<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

final readonly class Price
{
    public function __construct(
        public int $cents,
    ) {
    }
}
