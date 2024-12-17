<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

final readonly class OverallDimensions
{
    public function __construct(
        public float $height,
        public float $width,
        public float $length,
        public float $weight,
    ) {
    }
}
