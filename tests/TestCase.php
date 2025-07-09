<?php

namespace MrIncognito\CrudGenerator\Tests;

use MrIncognito\CrudGenerator\ApiCrudGeneratorServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ApiCrudGeneratorServiceProvider::class,
        ];
    }
}
