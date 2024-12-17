<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\ValueObject;

final readonly class Name
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $secondName,
    ) {
    }
}
