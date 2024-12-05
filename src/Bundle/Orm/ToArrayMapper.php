<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Orm;

use EvolveORM\Hydrator;
use JsonException;
use RuntimeException;

/**
 * Маппер для преобразования JSON-строки в ассоциативный массив.
 */
readonly class ToArrayMapper extends Mapper
{
    /**
     * @param int $flags Битовая маска JSON констант для управления процессом декодирования.
     *                   По умолчанию установлено JSON_THROW_ON_ERROR для генерации исключений при ошибках.
     */
    public function __construct(
        public int $flags = JSON_THROW_ON_ERROR,
    ) {
    }

    /**
     * Преобразует JSON-строку в ассоциативный массив.
     *
     * @inheritDoc
     * @noinspection PhpDocRedundantThrowsInspection
     * @param mixed $rawData Исходные данные в виде JSON-строки.
     * @param Hydrator $hydrator Объект гидратора (не используется в данной реализации).
     * @return array<mixed> Ассоциативный массив, полученный из JSON-строки.
     * @throws JsonException Если возникла ошибка при декодировании JSON.
     * @throws RuntimeException Если входные данные не являются строкой.
     */
    public function __invoke(mixed $rawData, Hydrator $hydrator): array
    {
        if (!is_string($rawData)) {
            throw new RuntimeException(
                sprintf(
                    '[%s] expected string, got [%s]',
                    static::class,
                    gettype($rawData),
                )
            );
        }

        /** @noinspection JsonEncodingApiUsageInspection */
        return (array)json_decode(
            json: $rawData,
            associative: true,
            flags: $this->flags,
        );
    }
}
