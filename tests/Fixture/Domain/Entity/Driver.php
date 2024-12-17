<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Domain\Entity;

use EvolveORM\Bundle\Attribute\OrmMapper;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Id;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Name;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Phone;
use EvolveORM\Tests\Fixture\Orm\ToNullablePhoneMapper;

final readonly class Driver
{
    public function __construct(
        public Id $id,
        public Name $name,
        #[OrmMapper(ToNullablePhoneMapper::class)]
        public ?Phone $phone,
    ) {
    }
}
