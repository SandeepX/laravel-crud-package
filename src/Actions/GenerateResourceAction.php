<?php

namespace MrIncognito\CrudGenerator\Actions;

use Illuminate\Support\Facades\File;

class GenerateResourceAction
{
    public function execute(string $name): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/resource.stub');
        $output = str_replace('{{ class }}', $name, $stub);
        File::ensureDirectoryExists(app_path('Http/Resources'));
        File::put(app_path("Http/Resources/{$name}Resource.php"), $output);
    }

}