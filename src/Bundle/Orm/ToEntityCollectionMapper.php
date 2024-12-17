<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Orm;

use EvolveORM\Bundle\Exception\HydrationException;
use EvolveORM\Hydrator;
use RuntimeException;

/**
 * Маппер для создания коллекции объектов указанного класса
 * на основе входных данных в виде массива массивов.
 */
readonly abstract class ToEntityCollectionMapper extends Mapper
{
    /**
     * @param class-string<object> $className Имя класса сущности, объекты которого будут создаваться.
     */
    public function __construct(
        private string $className,
    ) {
    }

    /**
     * @inheritDoc
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     * @param mixed $rawData Исходные данные в виде массива массивов.
     * @param Hydrator $hydrator Объект гидратора, используемый для создания сущностей.
     * @return array<object> Коллекция объектов указанного класса.
     * @throws RuntimeException Если входные данные не являются массивом.
     * @throws HydrationException Если возникла ошибка при гидрации объектов.
     */
    public function __invoke(mixed $rawData, Hydrator $hydrator): array
    {
        if (!is_array($rawData)) {
            throw new RuntimeException(
                sprintf(
                    '[%s] expected array, got [%s]',
                    static::class,
                    gettype($rawData),
                )
            );
        }

        return $hydrator->hydrateAll($this->className, $rawData);
    }
}
