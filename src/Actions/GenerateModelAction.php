<?php

namespace MrIncognito\CrudGenerator\Actions;

use Illuminate\Support\Facades\File;

class GenerateModelAction
{
    public function execute(string $name, string $rawFields): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/model.stub');

        $fields = explode(';', $rawFields);

        $excludedColumns = ['id', 'created_at', 'updated_at'];

        $fillableFields = collect($fields)
            ->map(function ($field) {
                $parts = explode(':', $field, 2);

                return trim($parts[0]);
            })
            ->filter(fn ($field) => ! in_array($field, $excludedColumns, true))
            ->map(fn ($field) => "        '{$field}'")
            ->implode(",\n");

        $output = str_replace(
            ['{{ class }}', '{{ fillable }}'],
            [$name, $fillableFields],
            $stub
        );

        File::ensureDirectoryExists(app_path('Models'));
        File::put(app_path("Models/{$name}.php"), $output);
    }
}