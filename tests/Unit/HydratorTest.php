<?php

declare(strict_types=1);

namespace EvolveORM\Tests\Unit;

use EvolveORM\Bundle\Exception\HydrationException;
use EvolveORM\Hydrator;
use EvolveORM\Tests\Fixture\BuiltInType\Enumable;
use EvolveORM\Tests\Fixture\BuiltInType\NullableBool;
use EvolveORM\Tests\Fixture\BuiltInType\NullableFloat;
use EvolveORM\Tests\Fixture\BuiltInType\NullableInt;
use EvolveORM\Tests\Fixture\BuiltInType\NullableString;
use EvolveORM\Tests\Fixture\BuiltInType\NullableUnion;
use EvolveORM\Tests\Fixture\BuiltInType\StrictArray;
use EvolveORM\Tests\Fixture\BuiltInType\StrictBool;
use EvolveORM\Tests\Fixture\BuiltInType\StrictFloat;
use EvolveORM\Tests\Fixture\BuiltInType\StrictInt;
use EvolveORM\Tests\Fixture\BuiltInType\StrictString;
use EvolveORM\Tests\Fixture\BuiltInType\StrictUnion;
use PHPUnit\Framework\TestCase;

/**
 * @covers \EvolveORM\Hydrator
 */
class HydratorTest extends TestCase
{
    protected Hydrator $hydrator;

    /** @throws HydrationException */
    public function setUp(): void
    {
        $this->hydrator = Hydrator::create();

        parent::setUp();
    }

    /** @throws HydrationException */
    public function testStrictUnion(): void
    {
        $object = $this->hydrator->hydrate(StrictUnion::class, ['value' => 1]);
        $this->assertInstanceOf(StrictUnion::class, $object);
        $this->assertSame(1, $object->value);

        $object = $this->hydrator->hydrate(StrictUnion::class, ['value' => 1.11]);
        $this->assertInstanceOf(StrictUnion::class, $object);
        $this->assertSame(1.11, $object->value);

        $object = $this->hydrator->hydrate(StrictUnion::class, ['value' => '1.11']);
        $this->assertInstanceOf(StrictUnion::class, $object);
        $this->assertSame('1.11', $object->value);

        $object = $this->hydrator->hydrate(StrictUnion::class, ['value' => false]);
        $this->assertInstanceOf(StrictUnion::class, $object);
        $this->assertFalse($object->value);

        $object = $this->hydrator->hydrate(StrictUnion::class, ['value' => [1]]);
        $this->assertInstanceOf(StrictUnion::class, $object);
        $this->assertSame([1], $object->value);

        try {
            $this->hydrator->hydrate(StrictUnion::class, ['value' => null]);
        } catch (HydrationException $exception) {
            $this->assertSame(
                'Не удалось найти подходящую стратегию гидрации для свойства [value] объекта [EvolveORM\Tests\Fixture\BuiltInType\StrictUnion]',
                $exception->getPrevious()?->getMessage(),
            );
        }
    }

    /** @throws HydrationException */
    public function testNullableUnion(): void
    {
        $object = $this->hydrator->hydrate(NullableUnion::class, ['value' => 1]);
        $this->assertInstanceOf(NullableUnion::class, $object);
        $this->assertSame(1, $object->value);

        $object = $this->hydrator->hydrate(NullableUnion::class, ['value' => 1.11]);
        $this->assertInstanceOf(NullableUnion::class, $object);
        $this->assertSame(1.11, $object->value);

        $object = $this->hydrator->hydrate(NullableUnion::class, ['value' => '1.11']);
        $this->assertInstanceOf(NullableUnion::class, $object);
        $this->assertSame('1.11', $object->value);

        $object = $this->hydrator->hydrate(NullableUnion::class, ['value' => false]);
        $this->assertInstanceOf(NullableUnion::class, $object);
        $this->assertFalse($object->value);

        $object = $this->hydrator->hydrate(NullableUnion::class, ['value' => [1]]);
        $this->assertInstanceOf(NullableUnion::class, $object);
        $this->assertSame([1], $object->value);

        $object = $this->hydrator->hydrate(NullableUnion::class, ['value' => null]);
        $this->assertInstanceOf(NullableUnion::class, $object);
        $this->assertNull($object->value);
    }

    /** @throws HydrationException */
    public function testStrictInt(): void
    {
        $object = $this->hydrator->hydrate(StrictInt::class, ['value' => 1]);
        $this->assertInstanceOf(StrictInt::class, $object);
        $this->assertSame(1, $object->value);

        $object = $this->hydrator->hydrate(StrictInt::class, ['value' => true]);
        $this->assertInstanceOf(StrictInt::class, $object);
        $this->assertSame(1, $object->value);

        $object = $this->hydrator->hydrate(StrictInt::class, ['value' => '']);
        $this->assertInstanceOf(StrictInt::class, $object);
        $this->assertSame(0, $object->value);

        $object = $this->hydrator->hydrate(StrictInt::class, ['value' => 1.01]);
        $this->assertInstanceOf(StrictInt::class, $object);
        $this->assertSame(1, $object->value);

        try {
            $this->hydrator->hydrate(StrictInt::class, ['value' => null]);
        } catch (HydrationException $exception) {
            $this->assertSame(
                'Не удалось найти подходящую стратегию гидрации для свойства [value] объекта [EvolveORM\Tests\Fixture\BuiltInType\StrictInt]',
                $exception->getPrevious()?->getMessage(),
            );
        }
    }

    /** @throws HydrationException */
    public function testNullableInt(): void
    {
        $object = $this->hydrator->hydrate(NullableInt::class, ['value' => 1]);
        $this->assertInstanceOf(NullableInt::class, $object);
        $this->assertSame(1, $object->value);

        $object = $this->hydrator->hydrate(NullableInt::class, ['value' => null]);
        $this->assertInstanceOf(NullableInt::class, $object);
        $this->assertNull($object->value);
    }

    /** @throws HydrationException */
    public function testStrictString(): void
    {
        $object = $this->hydrator->hydrate(StrictString::class, ['value' => 'StrictString']);
        $this->assertInstanceOf(StrictString::class, $object);
        $this->assertSame('StrictString', $object->value);

        try {
            $this->hydrator->hydrate(StrictString::class, ['value' => null]);
        } catch (HydrationException $exception) {
            $this->assertSame(
                'Не удалось найти подходящую стратегию гидрации для свойства [value] объекта [EvolveORM\Tests\Fixture\BuiltInType\StrictString]',
                $exception->getPrevious()?->getMessage(),
            );
        }
    }

    /** @throws HydrationException */
    public function testNullableString(): void
    {
        $object = $this->hydrator->hydrate(NullableString::class, ['value' => 'StrictString']);
        $this->assertInstanceOf(NullableString::class, $object);
        $this->assertSame('StrictString', $object->value);

        $object = $this->hydrator->hydrate(NullableString::class, ['value' => null]);
        $this->assertInstanceOf(NullableString::class, $object);
        $this->assertNull($object->value);
    }

    /** @throws HydrationException */
    public function testStrictFloat(): void
    {
        $object = $this->hydrator->hydrate(StrictFloat::class, ['value' => 1.11]);
        $this->assertInstanceOf(StrictFloat::class, $object);
        $this->assertSame(1.11, $object->value);

        try {
            $this->hydrator->hydrate(StrictFloat::class, ['value' => null]);
        } catch (HydrationException $exception) {
            $this->assertSame(
                'Не удалось найти подходящую стратегию гидрации для свойства [value] объекта [EvolveORM\Tests\Fixture\BuiltInType\StrictFloat]',
                $exception->getPrevious()?->getMessage(),
            );
        }
    }

    /** @throws HydrationException */
    public function testNullableFloat(): void
    {
        $object = $this->hydrator->hydrate(NullableFloat::class, ['value' => 1.11]);
        $this->assertInstanceOf(NullableFloat::class, $object);
        $this->assertSame(1.11, $object->value);

        $object = $this->hydrator->hydrate(NullableFloat::class, ['value' => null]);
        $this->assertInstanceOf(NullableFloat::class, $object);
        $this->assertNull($object->value);
    }

    /** @throws HydrationException */
    public function testStrictBool(): void
    {
        $object = $this->hydrator->hydrate(StrictBool::class, ['value' => false]);
        $this->assertInstanceOf(StrictBool::class, $object);
        $this->assertFalse($object->value);

        try {
            $this->hydrator->hydrate(StrictBool::class, ['value' => null]);
        } catch (HydrationException $exception) {
            $this->assertSame(
                'Не удалось найти подходящую стратегию гидрации для свойства [value] объекта [EvolveORM\Tests\Fixture\BuiltInType\StrictBool]',
                $exception->getPrevious()?->getMessage(),
            );
        }
    }

    /** @throws HydrationException */
    public function testNullableBool(): void
    {
        $object = $this->hydrator->hydrate(NullableBool::class, ['value' => true]);
        $this->assertInstanceOf(NullableBool::class, $object);
        $this->assertTrue($object->value);

        $object = $this->hydrator->hydrate(NullableBool::class, ['value' => null]);
        $this->assertInstanceOf(NullableBool::class, $object);
        $this->assertNull($object->value);
    }

    /** @throws HydrationException */
    public function testStrictArray(): void
    {
        $object = $this->hydrator->hydrate(StrictArray::class, ['value' => 1]);
        $this->assertInstanceOf(StrictArray::class, $object);
        $this->assertSame([1], $object->value);

        try {
            $this->hydrator->hydrate(StrictArray::class, ['value' => null]);
        } catch (HydrationException $exception) {
            $this->assertSame(
                'Не удалось найти подходящую стратегию гидрации для свойства [value] объекта [EvolveORM\Tests\Fixture\BuiltInType\StrictArray]',
                $exception->getPrevious()?->getMessage(),
            );
        }
    }

    /** @throws HydrationException */
    public function testEnumable(): void
    {
        $object = $this->hydrator->hydrate(Enumable::class, ['intEnum' => 1, 'stringEnum' => 'one']);
        $this->assertInstanceOf(Enumable::class, $object);
        $this->assertSame(1, $object->intEnum->value);
        $this->assertSame('one', $object->stringEnum->value);

        try {
            $this->hydrator->hydrate(Enumable::class, ['intEnum' => 111, 'stringEnum' => '111']);
        } catch (HydrationException $exception) {
            $this->assertSame(
                '111 is not a valid backing value for enum EvolveORM\Tests\Fixture\BuiltInType\IntEnum',
                $exception->getPrevious()?->getMessage(),
            );
        }
    }
}
