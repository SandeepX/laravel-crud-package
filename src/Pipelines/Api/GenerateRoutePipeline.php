<?php

namespace MrIncognito\CrudGenerator\Pipelines\Api;

use MrIncognito\CrudGenerator\Actions\Api\AppendRoutesAction;

class GenerateRoutePipeline
{
    public function __construct(protected AppendRoutesAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
