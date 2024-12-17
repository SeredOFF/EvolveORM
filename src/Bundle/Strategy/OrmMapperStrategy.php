<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Strategy;

use EvolveORM\Bundle\Attribute\OrmMapper;
use EvolveORM\Bundle\Orm\Mapper;
use EvolveORM\Hydrator;
use ReflectionProperty;

/**
 * Стратегия гидрации с использованием пользовательских ORM мапперов.
 *
 * Эта стратегия позволяет применять пользовательские мапперы для гидрации свойств объекта,
 * используя конфигурацию ORM или атрибуты.
 */
class OrmMapperStrategy implements HydrationStrategy
{
    /** @var array<class-string, Mapper> Кэш инстансов мапперов. Ключи — Mapper::class, значения — объекты Mapper */
    private array $mappers = [];

    /**
     * @param Hydrator $hydrator Гидратор для использования в мапперах.
     * @param array<class-string, array<string, class-string>> $ormConfig Конфигурация ORM.
     */
    public function __construct(
        private readonly Hydrator $hydrator,
        private readonly array $ormConfig = [],
    ) {
    }

    /**
     * @inheritDoc
     * @return bool True, если для свойства определен подходящий маппер.
     */
    public function canHydrate(ReflectionProperty $property, mixed $value): bool
    {
        $mapperClassName = $this->getMapperClassName($property);

        return !$property->isStatic()
            && $mapperClassName !== null
            && class_exists($mapperClassName)
            && is_a($mapperClassName, Mapper::class, true);
    }

    /** @inheritDoc */
    public function hydrate(object $object, ReflectionProperty $property, mixed $value): void
    {
        /** @var class-string $mapperClassName */
        $mapperClassName = $this->getMapperClassName($property);
        $mapper = $this->getMapper($mapperClassName);

        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $property->setValue($object, $mapper($value, $this->hydrator));
    }

    /**
     * Получает имя класса маппера для указанного свойства.
     *
     * @param ReflectionProperty $property Рефлексия свойства объекта.
     * @return string|null Имя класса маппера или null, если маппер не определен.
     */
    private function getMapperClassName(ReflectionProperty $property): ?string
    {
        $mapperClassName = $this->ormConfig[$property->class][$property->getName()] ?? null;

        if ($mapperClassName !== null) {
            return $mapperClassName;
        }

        $attribute = current($property->getAttributes(OrmMapper::class));
        if ($attribute === false) {
            return null;
        }

        $attribute = $attribute->newInstance();
        if (!($attribute instanceof OrmMapper)) {
            return null;
        }

        return $attribute->mapperClassName;
    }

    /**
     * Получает или создает экземпляр маппера по имени класса.
     *
     * @param class-string $mapperClassName Имя класса маппера.
     * @return Mapper Экземпляр маппера.
     */
    private function getMapper(string $mapperClassName): Mapper
    {
        if (!isset($this->mappers[$mapperClassName])) {
            /** @var Mapper $mapper */
            $mapper = new $mapperClassName();

            $this->mappers[$mapperClassName] = $mapper;
        }

        /** @var Mapper */
        return $this->mappers[$mapperClassName];
    }
}
