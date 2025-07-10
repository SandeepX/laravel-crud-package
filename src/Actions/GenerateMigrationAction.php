<?php

namespace MrIncognito\CrudGenerator\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateMigrationAction
{
    public function execute(string $name, string $rawFields): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/migration.stub');

        $tableName = Str::snake(Str::pluralStudly($name));
        $fields = explode(';', $rawFields);

        $migrationFields = collect($fields)->map(function ($field) {
            [$fieldName, $typeAndRules] = explode(':', $field, 2);

            $parts = explode('|', $typeAndRules);
            $type = $parts[0];
            $isNullable = Str::endsWith($type, '~');
            $baseType = rtrim($type, '~');

            $defaultValue = null;

            foreach ($parts as $rule) {
                if (Str::startsWith($rule, 'default:')) {
                    $defaultValue = Str::after($rule, 'default:');
                }
            }

            if ($baseType === 'foreign') {
                $statement = "\$table->foreignId('".trim($fieldName)."')";
                if ($isNullable) {
                    $statement .= '->nullable()';
                }

                foreach ($parts as $rule) {
                    if (Str::startsWith($rule, 'constrained:')) {
                        $targetTable = trim(Str::after($rule, 'constrained:'));
                        $statement .= "->constrained('{$targetTable}')";
                    } elseif ($rule === 'constrained') {
                        $statement .= '->constrained()';
                    } elseif (Str::startsWith($rule, 'onDelete:')) {
                        $action = trim(Str::after($rule, 'onDelete:'));
                        $statement .= "->onDelete('{$action}')";
                    }
                }

                return $statement.';';
            }

            $statement = "\$table->{$baseType}('{$fieldName}')";
            if ($isNullable) {
                $statement .= '->nullable()';
            }

            if (! is_null($defaultValue)) {
                // Wrap strings in quotes, leave numbers/booleans as-is
                $statement .= is_numeric($defaultValue) || in_array($defaultValue, ['true', 'false'])
                    ? "->default({$defaultValue})"
                    : "->default('{$defaultValue}')";
            }

            return $statement.';';
        })->map(fn ($line) => '            '.$line)
            ->implode("\n");

        $output = str_replace(
            ['{{ class }}', '{{ table }}', '{{ fields }}'],
            ['Create'.Str::studly($tableName).'Table', $tableName, $migrationFields],
            $stub
        );

        File::ensureDirectoryExists(database_path('migrations'));
        $timestamp = date('Y_m_d_His');
        File::put(database_path("migrations/{$timestamp}_create_{$tableName}_table.php"), $output);
    }
}
