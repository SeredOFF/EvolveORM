<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Strategy;

use EvolveORM\Bundle\Cache\ReflectionCache;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * Стратегия гидрации для значений внутренних (встроенных) классов PHP.
 *
 * Эта стратегия обрабатывает гидрацию свойств объекта, имеющих тип внутреннего класса PHP,
 * такие как DateTime, DateTimeImmutable и другие.
 */
readonly class InternalClassStrategy implements HydrationStrategy
{
    /**
     * @param ReflectionCache $reflectionCache Кэш рефлексии для оптимизации производительности.
     */
    public function __construct(
        private ReflectionCache $reflectionCache,
    ) {
    }

    /**
     * @inheritDoc
     * @return bool True, если свойство имеет тип внутреннего класса PHP и значение соответствует требованиям.
     */
    public function canHydrate(ReflectionProperty $property, mixed $value): bool
    {
        return !$property->isStatic()
            && $property->getType() instanceof ReflectionNamedType
            && class_exists($property->getType()->getName())
            && (
                ($value === null && $property->getType()->allowsNull())
                || $value !== null
            )
            && $this->reflectionCache
                ->getOrCreate($property->getType()->getName())
                ->isInternal();
    }

    /**
     * @inheritDoc
     * @param mixed $value Значение для гидрации (ожидается null, массив или скалярное значение).
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
        /** @var class-string<object> $className */
        $className = $type->getName();

        $reflection = $this->reflectionCache->getOrCreate($className);

        $property->setValue(
            $object,
            is_array($value)
                ? $reflection->newInstance(...$value)
                : $reflection->newInstance($value),
        );
    }
}
