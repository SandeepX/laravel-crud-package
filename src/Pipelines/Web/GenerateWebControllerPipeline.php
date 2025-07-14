<?php

namespace MrIncognito\CrudGenerator\Pipelines\Web;

use Closure;
use MrIncognito\CrudGenerator\Actions\Web\GenerateWebControllerAction;

class GenerateWebControllerPipeline
{
    public function __construct(protected GenerateWebControllerAction $action) {}

    public function handle(array $data, Closure $next)
    {
        $this->action->execute(name: $data['name']);

        return $next($data);
    }
}
