<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Orm;

use EvolveORM\Bundle\Orm\ToNullableValueObjectMapper;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Ticket;

readonly class ToNullableTicketMapper extends ToNullableValueObjectMapper
{
    public function __construct()
    {
        parent::__construct(
            Ticket::class,
            static fn(array $rawData): bool => empty($rawData['price']['cents']),
        );
    }
}
