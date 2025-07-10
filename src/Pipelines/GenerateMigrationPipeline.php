<?php

namespace MrIncognito\CrudGenerator\Pipelines;

use MrIncognito\CrudGenerator\Actions\GenerateMigrationAction;

class GenerateMigrationPipeline
{
    public function __construct(protected GenerateMigrationAction $action) {}

    public function handle(array $data, \Closure $next)
    {
        $this->action->execute(name: $data['name'], rawFields: $data['fields']);

        return $next($data);
    }
}
