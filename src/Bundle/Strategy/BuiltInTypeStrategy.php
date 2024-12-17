<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Strategy;

use ReflectionNamedType;
use ReflectionProperty;

/**
 * Стратегия гидрации для значений встроенных типов PHP.
 *
 * Эта стратегия обрабатывает гидрацию свойств объекта, имеющих встроенные типы PHP
 * (такие как int, string, bool и т.д.).
 */
class BuiltInTypeStrategy implements HydrationStrategy
{
    /** @inheritDoc */
    public function canHydrate(ReflectionProperty $property, mixed $value): bool
    {
        return !$property->isStatic()
            && $property->getType() instanceof ReflectionNamedType
            && $property->getType()->isBuiltin()
            && (
                ($value === null && $property->getType()->allowsNull())
                || $value !== null
            );
    }

    /** @inheritDoc */
    public function hydrate(object $object, ReflectionProperty $property, mixed $value): void
    {
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        if ($value !== null) {
            /** @var ReflectionNamedType $type */
            $type = $property->getType();

            settype($value, $type->getName());
        }

        $property->setValue($object, $value);
    }
}
