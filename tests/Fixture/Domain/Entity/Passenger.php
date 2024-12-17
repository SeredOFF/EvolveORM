<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\Entity;

use EvolveORM\Bundle\Attribute\OrmMapper;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Id;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Seat;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Ticket;
use EvolveORM\Tests\Fixture\Orm\ToLuggageCollectionMapper;
use EvolveORM\Tests\Fixture\Orm\ToNullableTicketMapper;

final readonly class Passenger
{
    /** @var Luggage[] */
    #[OrmMapper(ToLuggageCollectionMapper::class)]
    public array $luggage;

    public function __construct(
        public Id $id,
        public Seat $seat,
        #[OrmMapper(ToNullableTicketMapper::class)]
        public ?Ticket $ticket,
        Luggage ...$luggage
    ) {
        $this->luggage = $luggage;
    }
}
