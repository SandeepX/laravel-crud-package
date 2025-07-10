<?php

namespace MrIncognito\CrudGenerator\Pipelines;

use MrIncognito\CrudGenerator\Actions\GenerateControllerAction;

class GenerateControllerPipeline
{
    public function __construct(protected GenerateControllerAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
