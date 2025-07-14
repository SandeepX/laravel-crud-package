<?php

namespace MrIncognito\CrudGenerator\Actions\Web;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateWebControllerAction
{
    public function execute(string $name): void
    {
        $stub = file_get_contents(__DIR__.'/../../../stubs/web_controller.stub');
        $model = Str::studly($name);
        $variable = Str::camel($name);
        $pluralVariable = Str::plural($model);
        $viewPath = Str::kebab(Str::plural($name));
        $routeName = $viewPath;
        $replaced = str_replace(
            [
                '{{ model }}',
                '{{ variable }}',
                '{{ pluralVariable }}',
                '{{ viewPath }}',
                '{{ routeName }}',
                '{{ class }}',
            ],
            [
                $model,
                $variable,
                $pluralVariable,
                $viewPath,
                $routeName,
            ],
            $stub
        );
        File::ensureDirectoryExists(app_path('Http/Controllers/Web'));
        File::put(app_path("Http/Controllers/Web/{$model}Controller.php"), $replaced);
    }
}
