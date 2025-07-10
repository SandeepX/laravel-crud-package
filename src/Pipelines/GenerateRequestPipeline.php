<?php

namespace MrIncognito\CrudGenerator\Pipelines;

use MrIncognito\CrudGenerator\Actions\GenerateRequestAction;

class GenerateRequestPipeline
{
    public function __construct(protected GenerateRequestAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name'], rawFields: $data['fields']);

        return $next($data);
    }
}
