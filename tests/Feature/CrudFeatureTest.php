<?php

namespace MrIncognito\CrudGenerator\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->modelName = 'TestBook';

    $this->generatedPaths = [
        base_path("app/Models/{$this->modelName}.php"),
        base_path("app/Http/Requests/{$this->modelName}Request.php"),
        base_path("app/Http/Resources/{$this->modelName}Resource.php"),
        base_path("app/Http/Controllers/Api/{$this->modelName}Controller.php"),
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

it('generates model, controller, request, resource, and migration files with proper validation and fillable', function () {
    $fields = 'title:string|min:3|max:255;email:string~|email;published:boolean';

    $this->artisan("make:crud {$this->modelName} --fields=\"{$fields}\"")
        ->assertExitCode(0)
        ->run();

    foreach ($this->generatedPaths as $file) {
        expect(File::exists($file))->toBeTrue("File {$file} does not exist.");
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

    $modelContent = File::get(base_path("app/Models/{$this->modelName}.php"));
    expect($modelContent)->toContain("'title'");
    expect($modelContent)->toContain("'email'");
    expect($modelContent)->toContain("'published'");
    expect($modelContent)->not()->toContain('id');
    expect($modelContent)->not()->toContain('created_at');
    expect($modelContent)->not()->toContain('updated_at');

    $requestContent = File::get(base_path("app/Http/Requests/{$this->modelName}Request.php"));
    expect($requestContent)->toContain("'title' => ['required', 'string', 'min:3', 'max:255']");
    expect($requestContent)->toContain("'email' => ['nullable', 'string', 'email']");
    expect($requestContent)->toContain("'published' => ['required', 'boolean']");

});
