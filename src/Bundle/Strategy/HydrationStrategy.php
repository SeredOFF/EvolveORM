<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Strategy;

use ReflectionException;
use ReflectionProperty;

/**
 * Интерфейс для способов гидрации свойств объектов.
 *
 * Определяет методы для проверки возможности гидрации свойства объекта
 * и выполнения самой гидрации.
 */
interface HydrationStrategy
{
    /**
     * Проверяет, может ли данная стратегия гидрировать указанное свойство объекта.
     *
     * @param ReflectionProperty $property Рефлексия свойства объекта.
     * @param mixed $value Значение для гидрации.
     * @return bool True, если стратегия может гидрировать свойство, иначе false.
     * @throws ReflectionException Если возникла ошибка при работе с рефлексией.
     */
    public function canHydrate(ReflectionProperty $property, mixed $value): bool;

    /**
     * Выполняет гидрацию указанного свойства объекта.
     *
     * @param object $object Объект, свойство которого нужно гидрировать.
     * @param ReflectionProperty $property Рефлексия свойства объекта.
     * @param mixed $value Значение для гидрации.
     * @throws ReflectionException Если возникла ошибка при работе с рефлексией.
     */
    public function hydrate(object $object, ReflectionProperty $property, mixed $value): void;
}
