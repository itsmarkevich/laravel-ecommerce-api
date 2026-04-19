<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Cast\RecastingRemovalRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
        __DIR__ . '/database',
    ])
    ->withRules([
        CompactToVariablesRector::class,
        RecastingRemovalRector::class,
    ])
    ->withCodeQualityLevel(3);
