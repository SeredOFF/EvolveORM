<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Cache;

use ReflectionClass;
use ReflectionException;

/**
 * Интерфейс для кэширования объектов рефлексии.
 */
interface ReflectionCache
{
    /**
     * Получает объект ReflectionClass из кэша или создает новый, если его нет в кэше.
     *
     * @template T of object
     * @param class-string<T> $className Полное имя класса, для которого нужен объект ReflectionClass.
     * @return ReflectionClass<T> Объект ReflectionClass для указанного класса.
     * @throws ReflectionException Если класс не существует или возникла ошибка при создании ReflectionClass.
     */
    public function getOrCreate(string $className): ReflectionClass;

    /**
     * Удаляет все закэшированные объекты ReflectionClass.
     */
    public function clear(): void;
}
