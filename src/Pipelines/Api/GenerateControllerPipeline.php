<?php

namespace MrIncognito\CrudGenerator\Pipelines\Api;

use MrIncognito\CrudGenerator\Actions\Api\GenerateControllerAction;

class GenerateControllerPipeline
{
    public function __construct(protected GenerateControllerAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
