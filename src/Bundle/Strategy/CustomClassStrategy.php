<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Strategy;

use EvolveORM\Bundle\Cache\ReflectionCache;
use EvolveORM\Bundle\Exception\HydrationException;
use EvolveORM\Hydrator;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * Стратегия гидрации для значений пользовательских классов.
 *
 * Эта стратегия обрабатывает гидрацию свойств объекта, имеющих тип пользовательского класса.
 * Она использует рефлексию и гидратор для создания и заполнения объектов вложенных классов.
 */
readonly class CustomClassStrategy implements HydrationStrategy
{
    /**
     * @param ReflectionCache $reflectionCache Кэш рефлексии для оптимизации производительности.
     * @param Hydrator $hydrator Гидратор для создания вложенных объектов.
     */
    public function __construct(
        private ReflectionCache $reflectionCache,
        private Hydrator $hydrator,
    ) {
    }

    /** @inheritDoc */
    public function canHydrate(ReflectionProperty $property, mixed $value): bool
    {
        return !$property->isStatic()
            && $property->getType() instanceof ReflectionNamedType
            && (
                ($value === null && $property->getType()->allowsNull())
                || is_array($value)
            )
            && class_exists($property->getType()->getName())
            && $this->reflectionCache
                ->getOrCreate($property->getType()->getName())
                ->isUserDefined();
    }

    /**
     * @inheritDoc
     * @throws HydrationException
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

        /** @var array<mixed> $value */
        $property->setValue($object, $this->hydrator->hydrate($className, $value));
    }
}
