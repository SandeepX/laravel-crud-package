<?php

namespace MrIncognito\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCrudCommand extends Command
{
    protected $signature = 'make:api-crud {name} {--fields=}';

    /**
     * Example usage:
     * php artisan make:api-crud Book --fields="title:string|min:3,max:255;email:string?|email;published:boolean"
     *
     * Explanation:
     * - title:string|min:3,max:255;
     *    Field "title" is a string with validation rules: minimum length 3, maximum length 255
     * - email:string?|email
     *    Field "email" is an optional string with email validation
     * - published:boolean
     *    Field "published" is a boolean
     */
    protected $description = 'Generate API CRUD controller, model, request, and resource';

    public function handle(): void
    {
        $name = Str::studly($this->argument('name'));
        $fieldsInput = $this->option('fields') ?? '';

        $this->generateModel($name, $this->option('fields'));
        $this->generateRequest($name, $fieldsInput);
        $this->generateResource($name);
        $this->generateController($name);
        $this->generateMigration($name, $fieldsInput);
        $this->appendRoutes($name);

        $this->info("API CRUD with controller, model, request model and migration for {$name} created.");
    }

    protected function generateModel(string $name, string $rawFields): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/model.stub');

        $fields = explode(';', $rawFields);

        $fillable = collect($fields)
            ->map(function ($field) {
                $parts = explode(':', $field, 2);

                return "'".trim($parts[0])."'";
            })
            ->implode(",\n        ");

        $output = str_replace(
            ['{{ class }}', '{{ fillable }}'],
            [$name, $fillable],
            $stub
        );

        File::ensureDirectoryExists(app_path('Models'));
        File::put(app_path("Models/{$name}.php"), $output);
    }

    protected function generateController(string $name): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/controller.stub');
        $output = str_replace('{{ class }}', $name, $stub);
        File::ensureDirectoryExists(app_path('Http/Controllers/Api'));
        File::put(app_path("Http/Controllers/Api/{$name}Controller.php"), $output);
    }

    protected function generateRequest(string $name, string $rawFields): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/request.stub');

        $fields = explode(';', $rawFields);

        $rulesString = collect($fields)
            ->map(function ($field) {
                [$fieldName, $rulePart] = explode(':', $field, 2);

                $typeAndRules = explode('|', $rulePart);
                $type = $typeAndRules[0];

                $isNullable = Str::endsWith($type, '?');
                $baseType = rtrim($type, '?');

                $rules = $isNullable ? 'nullable' : 'required';
                $rules .= "|{$baseType}";

                if (isset($typeAndRules[1])) {
                    $extraRules = str_replace([',', ' '], '|', $typeAndRules[1]);
                    $rules .= '|'.$extraRules;
                }

                return "            '{$fieldName}' => '{$rules}',";
            })
            ->implode("\n");

        $output = str_replace(
            ['{{ class }}', '{{ rules }}'],
            [$name, $rulesString],
            $stub
        );

        File::ensureDirectoryExists(app_path('Http/Requests'));
        File::put(app_path("Http/Requests/{$name}Request.php"), $output);
    }

    protected function generateResource(string $name): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/resource.stub');
        $output = str_replace('{{ class }}', $name, $stub);
        File::ensureDirectoryExists(app_path('Http/Resources'));
        File::put(app_path("Http/Resources/{$name}Resource.php"), $output);
    }

    protected function generateMigration(string $name, string $rawFields): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/migration.stub');

        $tableName = Str::snake(Str::pluralStudly($name));
        $fields = explode(';', $rawFields);

        $migrationFields = collect($fields)->map(function ($field) {
            [$fieldName, $typeAndRules] = explode(':', $field, 2);

            $parts = explode('|', $typeAndRules);
            $type = $parts[0];
            $isNullable = Str::endsWith($type, '?');
            $baseType = rtrim($type, '?');

            if ($baseType === 'foreign') {
                $statement = "\$table->foreignId('{$fieldName}')";

                if ($isNullable) {
                    $statement .= '->nullable()';
                }

                foreach ($parts as $rule) {
                    if (Str::startsWith($rule, 'constrained:')) {
                        $targetTable = Str::after($rule, 'constrained:');
                        $statement .= "->constrained('{$targetTable}')";
                    } elseif ($rule === 'constrained') {
                        $statement .= '->constrained()';
                    } elseif (Str::startsWith($rule, 'onDelete:')) {
                        $action = Str::after($rule, 'onDelete:');
                        $statement .= "->onDelete('{$action}')";
                    }
                }

                return $statement.';';
            }

            return "\$table->{$baseType}('{$fieldName}')".($isNullable ? '->nullable()' : '').';';
        })->implode("\n            ");

        $output = str_replace(
            ['{{ class }}', '{{ table }}', '{{ fields }}'],
            ['Create'.Str::studly($tableName).'Table', $tableName, $migrationFields],
            $stub
        );

        File::ensureDirectoryExists(database_path('migrations'));
        $timestamp = date('Y_m_d_His');
        File::put(database_path("migrations/{$timestamp}_create_{$tableName}_table.php"), $output);
    }

    protected function appendRoutes(string $name): void
    {
        $resource = Str::kebab(Str::pluralStudly($name));
        File::append(base_path('routes/api.php'), "\nRoute::apiResource('{$resource}', \\App\\Http\\Controllers\\Api\\{$name}Controller::class);");
    }
}
