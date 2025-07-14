<?php

namespace MrIncognito\CrudGenerator\Pipelines\Api;

use MrIncognito\CrudGenerator\Actions\Api\GenerateResourceAction;

class GenerateResourcePipeline
{
    public function __construct(protected GenerateResourceAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
