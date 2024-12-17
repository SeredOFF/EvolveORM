<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Strategy;

use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

/**
 * Стратегия гидрации для значений union типа.
 *
 * Эта стратегия обрабатывает гидрацию свойств объекта, имеющих union тип,
 * состоящий только из встроенных типов PHP (int, float, string, bool, array).
 * Ограничение обусловлено необходимостью присвоения данных как есть.
 * Иначе возникает неопределенность при соотношении типов.
 */
class UnionStrategy implements HydrationStrategy
{
    /** @inheritDoc */
    public function canHydrate(ReflectionProperty $property, mixed $value): bool
    {
        return !$property->isStatic()
            && $property->getType() instanceof ReflectionUnionType
            && count(
                array_filter(
                    $property->getType()->getTypes(),
                    static fn(ReflectionType $type): bool => method_exists($type, 'isBuiltin') && !$type->isBuiltin(),
                )
            ) === 0
            && (
                ($value === null && $property->getType()->allowsNull())
                || (
                    is_int($value)
                    || is_float($value)
                    || is_string($value)
                    || is_bool($value)
                    || is_array($value)
                )
            );
    }

    /**
     * @inheritDoc
     * @param mixed $value Значение для гидрации (ожидается null или значение одного из встроенных типов).
     */
    public function hydrate(object $object, ReflectionProperty $property, mixed $value): void
    {
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $property->setValue($object, $value);
    }
}
