<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Strategy;

use ReflectionNamedType;
use ReflectionProperty;

/**
 * Стратегия гидрации для значений типа Enum.
 *
 * Эта стратегия обрабатывает гидрацию свойств объекта, имеющих тип Enum.
 * Она поддерживает как целочисленные, так и строковые значения для создания экземпляров Enum.
 */
class EnumStrategy implements HydrationStrategy
{
    /** @inheritDoc */
    public function canHydrate(ReflectionProperty $property, mixed $value): bool
    {
        return !$property->isStatic()
            && $property->getType() instanceof ReflectionNamedType
            && enum_exists($property->getType()->getName())
            && method_exists($property->getType()->getName(), 'from')
            && (
                ($value === null && $property->getType()->allowsNull())
                || $value !== null
            )
            && (is_int($value) || is_string($value));
    }

    /**
     * @inheritDoc
     * @param mixed $value Значение для гидрации (ожидается null, int или string).
     */
    public function hydrate(object $object, ReflectionProperty $property, mixed $value): void
    {
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        if ($value === null) {
            $property->setValue($object, $value);
            return;
        }

        /** @var ReflectionNamedType $type */
        $type = $property->getType();
        $enumClassName = $type->getName();

        /** @noinspection PhpUndefinedMethodInspection */
        $property->setValue($object, $enumClassName::from($value));
    }
}
