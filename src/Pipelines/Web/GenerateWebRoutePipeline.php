<?php

namespace MrIncognito\CrudGenerator\Pipelines\Web;

use MrIncognito\CrudGenerator\Actions\Web\AppendWebRoutesAction;

class GenerateWebRoutePipeline
{
    public function __construct(protected AppendWebRoutesAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
