<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Orm;

use EvolveORM\Bundle\Orm\ToEntityCollectionMapper;
use EvolveORM\Tests\Fixture\Domain\Entity\Passenger;

readonly class ToPassengerCollectionMapper extends ToEntityCollectionMapper
{
    public function __construct()
    {
        parent::__construct(Passenger::class);
    }
}
