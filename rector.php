<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/** @noinspection PhpUnhandledExceptionInspection */
return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src'])
    ->withPhpSets(php82: true);
