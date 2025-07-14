<?php

namespace MrIncognito\CrudGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $this->modelName = 'ApiExample';
    $this->controllerPath = app_path("Http/Controllers/Api/{$this->modelName}Controller.php");
    $this->resourcePath = app_path("Http/Resources/{$this->modelName}Resource.php");
    $this->webControllerPath = app_path("Http/Controllers/Web/{$this->modelName}Controller.php");
    $this->viewPath = resource_path('views/'.Str::kebab(Str::plural($this->modelName)));

    File::delete($this->controllerPath);
    File::delete($this->resourcePath);
    File::delete($this->webControllerPath);
    if (File::isDirectory($this->viewPath)) {
        File::deleteDirectory($this->viewPath);
    }
});

afterEach(function () {
    File::delete($this->controllerPath);
    File::delete($this->resourcePath);
    File::delete($this->webControllerPath);
    if (File::isDirectory($this->viewPath)) {
        File::deleteDirectory($this->viewPath);
    }
});

it('does not generate any api-related files when type is web', function () {
    $fields = 'title:string;description:text';

    $this->artisan("make:crud {$this->modelName} --fields=\"{$fields}\" --type=web")
        ->assertExitCode(0);

    $apiController = base_path("app/Http/Controllers/Api/{$this->modelName}Controller.php");
    expect(File::exists($apiController))->toBeFalse('API controller should NOT be generated for type=web.');

    $apiResource = base_path("app/Http/Resources/{$this->modelName}Resource.php");
    expect(File::exists($apiResource))->toBeFalse('API resource should NOT be generated for type=web.');
});

it('generates API controller and resource only when type is api', function () {
    $fields = 'title:string;content:text';

    $this->artisan("make:crud {$this->modelName} --fields={$fields} --type=api")
        ->assertExitCode(0);

    expect(File::exists($this->controllerPath))->toBeTrue();
    expect(File::exists($this->resourcePath))->toBeTrue();

    $webController = base_path("app/Http/Controllers/Web/{$this->modelName}Controller.php");
    expect(File::exists($webController))->toBeFalse('Web controller should NOT be generated for type=api.');

    $viewPath = resource_path('views/'.Str::kebab(Str::plural($this->modelName)));
    expect(File::isDirectory($viewPath))->toBeFalse('Blade view directory should NOT be generated for type=api.');
});
