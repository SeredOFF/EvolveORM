<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\Entity;

use EvolveORM\Bundle\Attribute\OrmMapper;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Duration;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Id;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Point;
use EvolveORM\Tests\Fixture\Orm\ToPointsCollectionMapper;

final readonly class Route
{
    /** @var Point[] */
    #[OrmMapper(ToPointsCollectionMapper::class)]
    public array $points;

    public function __construct(
        public Id $id,
        public string $title,
        public Duration $duration,
        Point ...$point
    ) {
        $this->points = $point;
    }
}
