<?php

declare(strict_types=1);

namespace EvolveORM;

use EvolveORM\Bundle\Cache\ReflectionCache;
use EvolveORM\Bundle\Cache\WeakRefReflectionCache;
use EvolveORM\Bundle\Exception\HydrationException;
use EvolveORM\Bundle\Payload\HydrationPayloadProcessor;
use EvolveORM\Bundle\Strategy\BuiltInTypeStrategy;
use EvolveORM\Bundle\Strategy\CustomClassStrategy;
use EvolveORM\Bundle\Strategy\EnumStrategy;
use EvolveORM\Bundle\Strategy\HydrationStrategy;
use EvolveORM\Bundle\Strategy\InternalClassStrategy;
use EvolveORM\Bundle\Strategy\OrmMapperStrategy;
use EvolveORM\Bundle\Strategy\UnionStrategy;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Throwable;

/**
 * Класс Hydrator отвечает за гидрацию объектов из массивов данных.
 *
 * Использует различные стратегии гидрации для обработки разных типов свойств
 * и поддерживает пользовательские стратегии.
 */
class Hydrator
{
    /** @var array<class-string, array<int, ReflectionProperty>> Кэш метаданных классов */
    private array $classMetadata = [];
    /** @var HydrationStrategy[] Массив стратегий гидрации */
    private readonly array $strategies;

    /**
     * @param ReflectionCache $reflectionCache Кэш рефлексии для оптимизации производительности.
     * @param array<class-string, array<string, class-string>> $ormConfig Конфигурация ORM.
     * @param HydrationStrategy[] $customStrategies Массив пользовательских стратегий гидрации.
     * @throws HydrationException Если пользовательские стратегии не реализуют интерфейс HydrationStrategy.
     */
    public function __construct(
        private readonly ReflectionCache $reflectionCache,
        array $ormConfig = [],
        array $customStrategies = [],
    ) {
        if (
            count(
                array_filter(
                    $customStrategies,
                    static fn($strategy): bool => !($strategy instanceof HydrationStrategy),
                )
            ) > 0
        ) {
            throw new HydrationException(
                'Пользовательские стратегии гидрации должны быть экземплярами класса HydrationStrategy'
            );
        }

        $this->strategies = array_merge(
            $customStrategies,
            // preset
            [
                new OrmMapperStrategy($this, $ormConfig),
                new UnionStrategy(),
                new BuiltInTypeStrategy(),
                new EnumStrategy(),
                new InternalClassStrategy($this->reflectionCache),
                new CustomClassStrategy($this->reflectionCache, $this),
            ]
        );
    }

    /** @throws HydrationException */
    public static function create(): self
    {
        return new self(new WeakRefReflectionCache());
    }

    /**
     * Гидрирует массив объектов заданного класса.
     *
     * @param class-string<object> $className Имя класса гидрируемых объектов.
     * @param array $lookupValueMaps Массив плоских карт значений для гидрируемых объектов.
     * @return object[] Массив гидрированных объектов.
     * @throws HydrationException Если возникла ошибка при гидрации.
     */
    public function hydrateAll(string $className, array $lookupValueMaps): array
    {
        return array_map(
            fn(array $lookupValueMap): object => $this->hydrate($className, $lookupValueMap),
            $lookupValueMaps,
        );
    }

    /**
     * Гидрирует один объект заданного класса.
     *
     * @param class-string<object> $className Имя класса гидрируемого объекта.
     * @param array $lookupValueMap Плоская карта значений для гидрации.
     * @return object Гидрированный объект.
     * @throws HydrationException Если возникла ошибка при гидрации.
     */
    public function hydrate(string $className, array $lookupValueMap): object
    {
        try {
            $hydrationPayload = HydrationPayloadProcessor::process($lookupValueMap);
            $reflection = $this->reflectionCache->getOrCreate($className);
            $object = $reflection->newInstanceWithoutConstructor();

            foreach ($this->getClassMetadata($reflection) as $property) {
                $value = $hydrationPayload[$property->getName()] ?? null;

                $this->hydrateProperty($object, $property, $value);
            }

            return $object;
        } catch (Throwable $exception) {
            throw new HydrationException(
                sprintf('Не удалась гидрация объекта [%s]', $className),
                $exception,
            );
        }
    }

    /**
     * Получает метаданные класса (свойства) из кэша или создает их.
     *
     * @param ReflectionClass<object> $reflection Объект рефлексии класса.
     * @return array<int, ReflectionProperty> Массив свойств класса.
     */
    private function getClassMetadata(ReflectionClass $reflection): array
    {
        $className = $reflection->getName();

        if (!isset($this->classMetadata[$className])) {
            $properties = [];

            while (true) {
                foreach ($reflection->getProperties() as $property) {
                    $properties[$property->getName()] = $property;
                }

                /** @noinspection CallableParameterUseCaseInTypeContextInspection */
                $reflection = $reflection->getParentClass();
                if ($reflection === false) {
                    break;
                }
            }

            $this->classMetadata[$className] = array_values($properties);
        }

        return $this->classMetadata[$className];
    }

    /**
     * Гидрирует отдельное свойство объекта.
     *
     * @param object $object Объект для гидрации.
     * @param ReflectionProperty $property Свойство для гидрации.
     * @param mixed $value Значение для гидрации.
     * @throws ReflectionException|HydrationException Если не найдена подходящая стратегия гидрации.
     */
    private function hydrateProperty(object $object, ReflectionProperty $property, mixed $value): void
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->canHydrate($property, $value)) {
                $strategy->hydrate($object, $property, $value);
                return;
            }
        }

        throw new ReflectionException(
            sprintf(
                'Не удалось найти подходящую стратегию гидрации для свойства [%s] объекта [%s]',
                $property->getName(),
                $property->class,
            )
        );
    }
}
