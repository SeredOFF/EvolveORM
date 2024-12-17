<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

final readonly class Model
{
    public function __construct(
        public string $title,
        public int $year,
    ) {
    }
}
