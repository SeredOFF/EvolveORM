<?php

declare(strict_types=1);

namespace EvolveORM\Bundle\Exception;

use Exception;
use Throwable;

/**
 * Исключение, возникающее при ошибках гидрации объектов.
 */
class HydrationException extends Exception
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
