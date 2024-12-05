<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\Entity;

use EvolveORM\Tests\Fixture\Domain\ValueObject\Model;

final readonly class Bus
{
    public function __construct(
        public int $number,
        public Model $model,
    ) {
    }
}
