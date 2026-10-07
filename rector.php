<?php

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpSets(php82: true)
    ->withImportNames()
    ->withoutParallel()
    ->withCache(__DIR__ . '/.rector.cache');
