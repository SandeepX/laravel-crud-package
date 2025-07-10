<?php

namespace MrIncognito\CrudGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->modelName = 'Employee';

    foreach (File::files(database_path('migrations')) as $file) {
        if (Str::contains($file->getFilename(), 'create_employees_table')) {
            File::delete($file->getPathname());
        }
    }
});

afterEach(function () {
    foreach (File::files(database_path('migrations')) as $file) {
        if (Str::contains($file->getFilename(), 'create_employees_table')) {
            File::delete($file->getPathname());
        }
    }
});

it('generates migration fields with default values', function () {
    $fields = 'name:string|default:Unknown;age:integer|default:25;active:boolean|default:true;salary:decimal|default:1000.50';

    $this->artisan("make:crud {$this->modelName} --fields={$fields}")
        ->assertExitCode(0)
        ->run();

    $migrationFile = collect(File::files(database_path('migrations')))
        ->first(fn ($file) => Str::contains($file->getFilename(), 'create_employees_table'));

    expect($migrationFile)->not->toBeNull();

    $content = File::get($migrationFile->getPathname());

    expect($content)->toContain("\$table->string('name')->default('Unknown');");
    expect($content)->toContain("\$table->integer('age')->default(25);");
    expect($content)->toContain("\$table->boolean('active')->default(true);");
    expect($content)->toContain("\$table->decimal('salary')->default(1000.50);");
});
