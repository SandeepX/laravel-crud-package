<?php

namespace MrIncognito\CrudGenerator\Pipelines;

use MrIncognito\CrudGenerator\Actions\GenerateModelAction;

class GenerateModelPipeline
{
    public function __construct(protected GenerateModelAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name'], rawFields: $data['fields']);

        return $next($data);
    }
}
