<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Orm;

use EvolveORM\Bundle\Orm\ToEntityCollectionMapper;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Point;

readonly class ToPointsCollectionMapper extends ToEntityCollectionMapper
{
    public function __construct()
    {
        parent::__construct(Point::class);
    }
}
