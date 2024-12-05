<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Orm;

use Closure;
use EvolveORM\Bundle\Exception\HydrationException;
use EvolveORM\Hydrator;
use RuntimeException;

/**
 * Маппер для создания объекта-значения указанного класса на основе входных данных,
 * с возможностью возврата null при отсутствии данных.
 */
readonly abstract class ToNullableValueObjectMapper extends Mapper
{
    /**
     * @param class-string $className Полное имя класса объекта-значения, который будет создаваться.
     * @param Closure $isEmptyLookupValueMap Функция для проверки наличия исходных данных.
     *                                       Должна возвращать true, если данные отсутствуют.
     */
    public function __construct(
        private string $className,
        private Closure $isEmptyLookupValueMap,
    ) {
    }

    /**
     * @inheritDoc
     * @param mixed $rawData Исходные данные в виде массива.
     * @param Hydrator $hydrator Объект гидратора, используемый для создания объекта-значения.
     * @return object|null Объект-значение указанного класса или null, если данные отсутствуют.
     * @throws RuntimeException Если входные данные не являются массивом.
     * @throws HydrationException Если возникла ошибка при гидрации объекта.
     */
    public function __invoke(mixed $rawData, Hydrator $hydrator): ?object
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

        if (($this->isEmptyLookupValueMap)($rawData)) {
            return null;
        }

        return $hydrator->hydrate($this->className, $rawData);
    }
}
