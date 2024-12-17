<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

use DateTimeImmutable;

final readonly class Point
{
    public function __construct(
        public string $title,
        public float $latitude,
        public float $longitude,
        public DateTimeImmutable $arrivalTime,
    ) {
    }
}
