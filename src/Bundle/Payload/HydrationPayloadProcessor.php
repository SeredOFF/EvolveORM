<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Payload;

/**
 * Класс для преобразования плоских данных в иерархическую структуру для гидрации объектной модели.
 *
 * Предоставляет функциональность для трансформации плоского массива данных
 * в многоуровневую структуру, подходящую для гидрации объектов.
 *
 * @example
 * Входные данные:
 *     [
 *          'one_two' => 'one_two',
 *          'three_four_five' => 'three_four_five',
 *          'three_four_six' => 'three_four_six',
 *          'seven' => 'seven',
 *     ]
 * Выходные данные:
 *     [
 *          'one' => [
 *              'two' => 'one_two',
 *          ],
 *          'three' => [
 *              'four' => [
 *                  'five' => 'three_four_five',
 *                  'six' => 'three_four_six',
 *              ],
 *          ],
 *          'seven' => 'seven',
 *     ]
 *
 * Примечание: Процессор также может обрабатывать уже подготовленные структуры данных без побочных эффектов.
 * @see \EvolveORM\Tests\Unit\HydrationPayloadProcessorTest
 */
class HydrationPayloadProcessor
{
    /**
     * Преобразует плоский массив данных в иерархическую структуру.
     *
     * @param array<string, mixed> $lookupValueMap Плоская карта значений
     * @return array<string, mixed> Иерархическая структура данных, пригодная для гидрации объектной модели
     */
    public static function process(array $lookupValueMap): array
    {
        $hydrationPayload = [];

        foreach ($lookupValueMap as $alias => $data) {
            $valueMap = explode('_', $alias);

            if (count($valueMap) > 1) {
                $payloadItem = [];

                self::explodeValueMap($valueMap, $payloadItem, $data);

                $hydrationPayload[$valueMap[0]] = array_merge_recursive(
                    $hydrationPayload[$valueMap[0]] ?? [],
                    $payloadItem[$valueMap[0]],
                );

                continue;
            }

            $hydrationPayload[$valueMap[0]] = $data;
        }

        return $hydrationPayload;
    }

    /**
     * Рекурсивно создает иерархическую структуру из разделенных ключей.
     *
     * @param string[] $valueMap Массив ключей, полученных разделением исходного ключа
     * @param array<string, mixed> $payloadItem Ссылка на текущий элемент результирующего массива
     * @param mixed $val Значение для вставки в конечный узел
     */
    private static function explodeValueMap(array &$valueMap, array &$payloadItem, mixed $val): void
    {
        $current = current($valueMap);
        $next = next($valueMap);

        if ($next === false) {
            $payloadItem[$current] = $val;

            return;
        }

        $payloadItem[$current] = [];

        self::explodeValueMap($valueMap, $payloadItem[$current], $val);
    }
}
