<?php

namespace MrIncognito\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use MrIncognito\CrudGenerator\Pipelines\GenerateControllerPipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateMigrationPipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateModelPipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateRequestPipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateResourcePipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateRoutePipeline;

class GenerateCrudCommand extends Command
{
    protected $signature = 'make:crud {name} {--fields=}';

    protected $description = 'Generate API CRUD controller, model, request, route and resource';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $fieldsInput = $this->option('fields') ?? '';

        $data = [
            'name' => $name,
            'fields' => $fieldsInput,
        ];

        app(Pipeline::class)
            ->send($data)
            ->through([
                GenerateModelPipeline::class,
                GenerateRequestPipeline::class,
                GenerateControllerPipeline::class,
                GenerateResourcePipeline::class,
                GenerateMigrationPipeline::class,
                GenerateRoutePipeline::class,
            ])
            ->then(function () use ($name) {
                $this->info("API CRUD with controller, model, request, resource and migration for {$name} created.");
            });
    }
}
