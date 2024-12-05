<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Attribute;

use Attribute;

/**
 * Атрибут для определения пользовательского сервиса объектно-реляционного отображения свойства объекта.
 *
 * @package EvolveORM\Bundle\Attribute
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class OrmMapper
{
    /**
     * @param string $mapperClassName Полное имя класса маппера, который будет использоваться
     *                                для преобразования значения свойства объекта.
     */
    public function __construct(public string $mapperClassName)
    {
    }
}
