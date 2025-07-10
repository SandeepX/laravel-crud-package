<?php

namespace MrIncognito\CrudGenerator\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AppendRoutesAction
{
    public function execute(string $name): void
    {
        $resource = Str::kebab(Str::pluralStudly($name));
        File::append(base_path('routes/api.php'), "\nRoute::apiResource('{$resource}', \\App\\Http\\Controllers\\Api\\{$name}Controller::class);");
    }
}
