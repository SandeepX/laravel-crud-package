<?php

namespace MrIncognito\CrudGenerator\Pipelines\Web;

use Closure;
use MrIncognito\CrudGenerator\Actions\Web\GenerateViewAction;

class GenerateViewsPipeline
{
    public function __construct(protected GenerateViewAction $action) {}

    public function handle(array $data, Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
