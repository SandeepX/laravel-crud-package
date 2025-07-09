<?php

namespace MrIncognito\CrudGenerator;

use Illuminate\Support\ServiceProvider;
use MrIncognito\CrudGenerator\Commands\GenerateCrudCommand;

class ApiCrudGeneratorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCrudCommand::class,
            ]);
        }
    }
}
