<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\Orm;

use EvolveORM\Bundle\Orm\ToNullableValueObjectMapper;
use EvolveORM\Tests\Fixture\Domain\ValueObject\Phone;

readonly class ToNullablePhoneMapper extends ToNullableValueObjectMapper
{
    public function __construct()
    {
        parent::__construct(
            Phone::class,
            static fn(array $rawData): bool => empty($rawData['value']),
        );
    }
}
