<?php

namespace MrIncognito\CrudGenerator\Actions;

use Illuminate\Support\Facades\File;

class GenerateControllerAction
{
    public function execute(string $name): void
    {
        $stub = file_get_contents(__DIR__ . '/../../stubs/controller.stub');
        $output = str_replace('{{ class }}', $name, $stub);
        File::ensureDirectoryExists(app_path('Http/Controllers/Api'));
        File::put(app_path("Http/Controllers/Api/{$name}Controller.php"), $output);
    }

}