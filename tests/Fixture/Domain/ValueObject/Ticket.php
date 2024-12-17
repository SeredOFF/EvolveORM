<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

use DateTimeImmutable;

final readonly class Ticket
{
    public function __construct(
        public Price $price,
        public DateTimeImmutable $dateTime,
    ) {
    }
}
