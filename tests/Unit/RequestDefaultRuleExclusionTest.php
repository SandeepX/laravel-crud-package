<?php

namespace MrIncognito\CrudGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->modelName = 'Donor';
    $this->requestPath = app_path("Http/Requests/{$this->modelName}Request.php");

    if (File::exists($this->requestPath)) {
        File::delete($this->requestPath);
    }
});

afterEach(function () {
    if (File::exists($this->requestPath)) {
        File::delete($this->requestPath);
    }
});

it('excludes default values from validation rules in request', function () {
    $fields = 'name:string|default:Unknown;age:integer~;active:boolean|default:true;salary:decimal|default:1000.50';

    $this->artisan("make:crud {$this->modelName} --fields={$fields}")
        ->assertExitCode(0)
        ->run();

    expect(File::exists($this->requestPath))->toBeTrue();

    $requestContent = File::get($this->requestPath);

    expect($requestContent)->toContain("'name' => ['required', 'string']");
    expect($requestContent)->toContain("'age' => ['nullable', 'integer']");
    expect($requestContent)->toContain("'active' => ['required', 'boolean']");
    expect($requestContent)->toContain("'salary' => ['required', 'numeric']");

    expect($requestContent)->not->toContain('default:Unknown');
    expect($requestContent)->not->toContain('default:true');
    expect($requestContent)->not->toContain('default:1000.50');
});
