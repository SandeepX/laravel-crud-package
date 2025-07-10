<?php

namespace MrIncognito\CrudGenerator\Pipelines;

use MrIncognito\CrudGenerator\Actions\AppendRoutesAction;

class GenerateRoutePipeline
{
    public function __construct(protected AppendRoutesAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
