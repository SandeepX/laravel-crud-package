<?php

namespace MrIncognito\CrudGenerator\Actions\Web;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateViewAction
{
    public function execute(string $name): void
    {
        $viewPath = resource_path('views/'.Str::kebab(Str::plural($name)));
        File::ensureDirectoryExists($viewPath);

        $variable = Str::camel($name);
        $pluralVariable = Str::plural($variable);
        $routeName = Str::kebab(Str::plural($name));
        $viewStubPath = __DIR__.'/../../../stubs/views';

        $files = ['index', 'create', 'edit', 'show', '_form'];

        foreach ($files as $file) {
            $stub = file_get_contents("$viewStubPath/{$file}.stub");

            $content = str_replace(
                ['{{ variable }}', '{{ pluralVariable }}', '{{ routeName }}', '{{ viewPath }}'],
                [$variable, $pluralVariable, $routeName, Str::kebab(Str::plural($name))],
                $stub
            );

            File::put("{$viewPath}/{$file}.blade.php", $content);
        }
    }
}
