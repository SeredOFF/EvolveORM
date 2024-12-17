<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Fixture\BuiltInType;

final readonly class Enumable
{
    public function __construct(
        public IntEnum $intEnum,
        public StringEnum $stringEnum,
    ) {
    }
}
