<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Orm;

use EvolveORM\Bundle\Orm\ToEntityCollectionMapper;
use EvolveORM\Tests\Fixture\Domain\Entity\Luggage;

readonly class ToLuggageCollectionMapper extends ToEntityCollectionMapper
{
    public function __construct()
    {
        parent::__construct(Luggage::class);
    }
}
