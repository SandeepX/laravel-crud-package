<?php

namespace MrIncognito\CrudGenerator\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {

    $this->modelName = 'TestBook';

    $this->generatedPaths = [
        'model' => base_path("app/Models/{$this->modelName}.php"),
        'request' => base_path("app/Http/Requests/{$this->modelName}Request.php"),
        'resource' => base_path("app/Http/Resources/{$this->modelName}Resource.php"),
        'controller' => base_path("app/Http/Controllers/Api/{$this->modelName}Controller.php"),
    ];
});

afterEach(function () {
    foreach ($this->generatedPaths as $file) {
        if (File::exists($file)) {
            File::delete($file);
        }
    }

    $migrationFiles = File::files(database_path('migrations'));
    foreach ($migrationFiles as $file) {
        if (Str::contains($file->getFilename(), 'create_test_books_table')) {
            File::delete($file->getPathname());
        }
    }
});

it('generates all files (model, controller, request, resource, migration) for type api', function () {
    $fields = 'title:string|min:3|max:255;email:string~|email;published:boolean';

    $this->artisan("make:crud {$this->modelName} --fields=\"{$fields}\" --type=api")
        ->assertExitCode(0)
        ->run();

    foreach ($this->generatedPaths as $key => $file) {
        expect(File::exists($file))->toBeTrue("{$key} file does not exist.");
    }

    $migrationFiles = File::files(database_path('migrations'));
    $migrationFound = false;
    foreach ($migrationFiles as $file) {
        if (Str::contains($file->getFilename(), 'create_test_books_table')) {
            $migrationFound = true;
            $content = File::get($file->getPathname());

            expect($content)->toContain('$table->string(\'title\')');
            expect($content)->toContain('$table->string(\'email\')');
            expect($content)->toContain('$table->boolean(\'published\')');
        }
    }
    expect($migrationFound)->toBeTrue('Migration file was not created.');

    $requestContent = File::get($this->generatedPaths['request']);
    expect($requestContent)->toContain("'title' => ['required', 'string', 'min:3', 'max:255']");
    expect($requestContent)->toContain("'email' => ['nullable', 'string', 'email']");
    expect($requestContent)->toContain("'published' => ['required', 'boolean']");
});

it('excludes model and migration files when --exclude=model,migration is used with type api', function () {
    $fields = 'title:string|min:3|max:255;email:string~|email;published:boolean';

    $this->artisan("make:crud {$this->modelName} --fields=\"{$fields}\" --type=api --exclude=model,migration")
        ->assertExitCode(0)
        ->run();

    expect(File::exists($this->generatedPaths['model']))->toBeFalse('Model file should NOT exist.');

    $migrationFiles = File::files(database_path('migrations'));
    $migrationFound = false;
    foreach ($migrationFiles as $file) {
        if (Str::contains($file->getFilename(), 'create_test_books_table')) {
            $migrationFound = true;
        }
    }
    expect($migrationFound)->toBeFalse('Migration file should NOT exist.');

    foreach (['request', 'resource', 'controller'] as $key) {
        expect(File::exists($this->generatedPaths[$key]))->toBeTrue("{$key} file does not exist.");
    }

    $requestContent = File::get($this->generatedPaths['request']);
    expect($requestContent)->toContain("'title' => ['required', 'string', 'min:3', 'max:255']");
    expect($requestContent)->toContain("'email' => ['nullable', 'string', 'email']");
    expect($requestContent)->toContain("'published' => ['required', 'boolean']");
});
