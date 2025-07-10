<?php

namespace MrIncognito\CrudGenerator\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateRequestAction
{
    public function execute(string $name, string $rawFields): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/request.stub');

        $fields = explode(';', $rawFields);

        $rulesString = collect($fields)
            ->map(function ($field) {
                [$fieldName, $rulePart] = explode(':', $field, 2);

                $typeAndRules = explode('|', $rulePart);
                $type = $typeAndRules[0];

                $isNullable = Str::endsWith($type, '~');
                $baseType = rtrim($type, '~');

                $rules = $isNullable ? 'nullable' : 'required';

                if ($baseType === 'foreign') {
                    $table = null;
                    $column = 'id';
                    if (! $isNullable) {
                        foreach ($typeAndRules as $rule) {
                            if (str_starts_with($rule, 'constrained:')) {
                                $table = explode(':', $rule)[1];
                            } elseif ($rule !== 'foreign' && ! str_starts_with($rule, 'onDelete') && ! str_starts_with($rule, 'onUpdate')) {
                                if (str_contains($rule, ',')) {
                                    [$table, $column] = explode(',', $rule);
                                } elseif (! str_contains($rule, ':')) {
                                    $table = $rule;
                                }
                            }
                        }

                        if ($table) {
                            $rules .= "|exists:{$table},{$column}";
                        }
                    }
                } else {
                    $rules .= '|'.$this->mapToValidationRule($baseType);

                    if (isset($typeAndRules[1])) {
                        $extraRules = implode('|', array_slice($typeAndRules, 1));
                        $rules .= "|{$extraRules}";
                    }
                }

                return "    '{$fieldName}' => '{$rules}',";
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

    private function mapToValidationRule(string $dbType): string
    {
        return match ($dbType) {
            'bigIncrements', 'bigInteger', 'increments', 'integer',
            'mediumIncrements', 'mediumInteger', 'smallIncrements',
            'smallInteger', 'tinyIncrements', 'tinyInteger',
            'unsignedBigInteger', 'unsignedInteger', 'unsignedMediumInteger',
            'unsignedSmallInteger', 'unsignedTinyInteger', 'year' => 'integer',

            'decimal', 'double', 'float', 'unsignedDecimal' => 'numeric',

            'boolean' => 'boolean',

            'date', 'dateTime', 'time', 'timestamp', 'timeTz', 'timestampTz' => 'date',

            'json', 'jsonb' => 'array',

            default => 'string',
        };
    }
}
