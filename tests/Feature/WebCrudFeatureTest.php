<?php

namespace MrIncognito\CrudGenerator\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->modelName = 'WebArticle';

    $this->generatedPaths = [
        'model' => base_path("app/Models/{$this->modelName}.php"),
        'request' => base_path("app/Http/Requests/{$this->modelName}Request.php"),
        'controller' => base_path("app/Http/Controllers/Web/{$this->modelName}Controller.php"),
    ];

    $this->viewsPath = resource_path('views/'.Str::kebab(Str::plural($this->modelName)));
});

afterEach(function () {
    foreach ($this->generatedPaths as $path) {
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    foreach (File::files(database_path('migrations')) as $file) {
        if (Str::contains($file->getFilename(), 'create_web_articles_table')) {
            File::delete($file->getPathname());
        }
    }

    if (File::isDirectory($this->viewsPath)) {
        File::deleteDirectory($this->viewsPath);
    }
});

it('generates model, controller, request, migration, and blade views for web CRUD', function () {
    $fields = 'title:string;description:text';

    $this->artisan("make:crud {$this->modelName} --fields=\"{$fields}\" --type=web")
        ->assertExitCode(0);

    foreach (['model', 'request', 'controller'] as $key) {
        expect(File::exists($this->generatedPaths[$key]))->toBeTrue("{$key} was not generated.");
    }

    $migrationFound = false;
    foreach (File::files(database_path('migrations')) as $file) {
        if (Str::contains($file->getFilename(), 'create_web_articles_table')) {
            $migrationFound = true;
            $content = File::get($file->getPathname());

            expect($content)->toContain('$table->string(\'title\')');
            expect($content)->toContain('$table->text(\'description\')');
        }
    }
    expect($migrationFound)->toBeTrue('Migration was not generated.');

    $viewFiles = ['index', 'create', 'edit', 'show', '_form'];
    foreach ($viewFiles as $view) {
        expect(File::exists("{$this->viewsPath}/{$view}.blade.php"))
            ->toBeTrue("Blade view {$view}.blade.php not found.");
    }

    $controllerContent = File::get($this->generatedPaths['controller']);
    expect($controllerContent)->toContain("return view('web-articles.index");
    expect($controllerContent)->toContain("return view('web-articles.create");
});

it('excludes model and migration files when --exclude=model,migration is used with type web', function () {
    $fields = 'title:string;description:text';

    $this->artisan("make:crud {$this->modelName} --fields=\"{$fields}\" --type=web --exclude=model,migration")
        ->assertExitCode(0);

    expect(File::exists($this->generatedPaths['model']))->toBeFalse('Model file should NOT be generated.');

    $migrationFound = false;
    foreach (File::files(database_path('migrations')) as $file) {
        if (Str::contains($file->getFilename(), 'create_web_articles_table')) {
            $migrationFound = true;
        }
    }
    expect($migrationFound)->toBeFalse('Migration file should NOT be generated.');

    foreach (['request', 'controller'] as $key) {
        expect(File::exists($this->generatedPaths[$key]))->toBeTrue("{$key} was not generated.");
    }

    $viewFiles = ['index', 'create', 'edit', 'show', '_form'];
    foreach ($viewFiles as $view) {
        expect(File::exists("{$this->viewsPath}/{$view}.blade.php"))
            ->toBeTrue("Blade view {$view}.blade.php not found.");
    }
});
