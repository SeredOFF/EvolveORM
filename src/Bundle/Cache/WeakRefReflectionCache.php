<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Cache;

use ReflectionClass;
use ReflectionException;
use WeakReference;

/**
 * Реализация кэша объектов ReflectionClass с использованием слабых ссылок.
 *
 * Механизм кэширования на основе WeakReference для автоматического освобождения памяти,
 * когда объекты ReflectionClass больше не используются.
 */
class WeakRefReflectionCache implements ReflectionCache
{
    /** @var array<string, WeakReference<ReflectionClass<object>>> Кэш объектов ReflectionClass */
    private array $cache = [];

    /**
     * @inheritDoc
     * @template T of object
     * @return ReflectionClass<T>
     */
    public function getOrCreate(string $className): ReflectionClass
    {
        if (!class_exists($className)) {
            throw new ReflectionException(
                sprintf('Неизвестный className [%s]', $className)
            );
        }

        if (!isset($this->cache[$className])) {
            $this->cache[$className] = WeakReference::create(new ReflectionClass($className));
        }

        /** @var ReflectionClass<T> */
        return $this->cache[$className]->get() ?? new ReflectionClass($className);
    }

    /** @inheritDoc */
    public function clear(): void
    {
        $this->cache = array_filter(
            $this->cache,
            static fn(WeakReference $ref) => $ref->get() !== null,
        );
    }
}
