<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $config): void {
    $config->paths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $config->import(LaravelSetList::LARAVEL_100);

    $config->import(LevelSetList::UP_TO_PHP_84);

    $config->parallel();
};
