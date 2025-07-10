<?php

namespace MrIncognito\CrudGenerator\Pipelines;

use MrIncognito\CrudGenerator\Actions\GenerateResourceAction;

class GenerateResourcePipeline
{
    public function __construct(protected GenerateResourceAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
