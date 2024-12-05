<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\Aggregate;

use DateTimeImmutable;
use EvolveORM\Bundle\Attribute\OrmMapper;
use EvolveORM\Tests\Fixture\Domain\Entity\Bus;
use EvolveORM\Tests\Fixture\Domain\Entity\Driver;
use EvolveORM\Tests\Fixture\Domain\Entity\Passenger;
use EvolveORM\Tests\Fixture\Domain\Entity\Route;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Id;
use EvolveORM\Tests\Fixture\Orm\ToPassengerCollectionMapper;

final readonly class Trip
{
    /** @var Passenger[] */
    #[OrmMapper(ToPassengerCollectionMapper::class)]
    public array $passengers;

    public function __construct(
        public Id $id,
        public DateTimeImmutable $date,
        public Route $route,
        public Bus $bus,
        public Driver $driver,
        Passenger ...$passengers
    ) {
        $this->passengers = $passengers;
    }
}
