<?php

namespace MrIncognito\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use MrIncognito\CrudGenerator\Pipelines\Api\GenerateControllerPipeline;
use MrIncognito\CrudGenerator\Pipelines\Api\GenerateResourcePipeline;
use MrIncognito\CrudGenerator\Pipelines\Api\GenerateRoutePipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateMigrationPipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateModelPipeline;
use MrIncognito\CrudGenerator\Pipelines\GenerateRequestPipeline;
use MrIncognito\CrudGenerator\Pipelines\Web\GenerateViewsPipeline;
use MrIncognito\CrudGenerator\Pipelines\Web\GenerateWebControllerPipeline;
use MrIncognito\CrudGenerator\Pipelines\Web\GenerateWebRoutePipeline;

class GenerateCrudCommand extends Command
{
    protected $signature = 'make:crud 
        {name : The name of the CRUD resource}
        {--fields= : Comma-separated field definitions (e.g. title:string,~body:text)}
        {--type=api : The type of CRUD to generate (api or web)}
        {--exclude= : Comma-separated list of components to exclude (e.g. model,request,migration)}';

    protected $description = 'Generate a complete CRUD setup including model,controller, request, routes, views,
     and resource files based on the specified type (api or web). and with feature of excluding specific classes';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $fieldsInput = $this->option('fields') ?? '';
        $type = $this->option('type') ?? '';

        $exclude = collect(explode(',', $this->option('exclude') ?? ''))
            ->map(fn ($item) => strtolower(trim($item)))
            ->filter();

        $data = [
            'name' => $name,
            'fields' => $fieldsInput,
            'type' => $type,
        ];

        $pipelineMap = [
            'model' => GenerateModelPipeline::class,
            'request' => GenerateRequestPipeline::class,
            'migration' => GenerateMigrationPipeline::class,
            'controller' => $type === 'web' ? GenerateWebControllerPipeline::class : GenerateControllerPipeline::class,
            'route' => $type === 'web' ? GenerateWebRoutePipeline::class : GenerateRoutePipeline::class,
            'resource' => $type === 'web' ? null : GenerateResourcePipeline::class,
            'views' => $type === 'web' ? GenerateViewsPipeline::class : null,
        ];

        $pipelines = collect($pipelineMap)
            ->reject(fn ($class, $key) => $exclude->contains($key) || $class === null)
            ->values()
            ->toArray();

        app(Pipeline::class)
            ->send($data)
            ->through($pipelines)
            ->then(function () use ($data) {
                $this->info(strtoupper($data['type'])." CRUD for {$data['name']} generated successfully.");
            });
    }
}
