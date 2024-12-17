<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\Entity;

use EvolveORM\Tests\Fixture\Domain\Enum\Type;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Id;
use EvolveORM\Tests\Fixture\Domain\ValueObject\OverallDimensions;

final readonly class Luggage
{
    public function __construct(
        public Id $id,
        public Type $type,
        public OverallDimensions $overallDimensions,
    ) {
    }
}
