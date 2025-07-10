<?php

namespace MrIncognito\CrudGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
use MrIncognito\CrudGenerator\Actions\GenerateRequestAction;

beforeEach(function () {
    $this->className = 'TestDatatype';
    $this->filePath = app_path("Http/Requests/{$this->className}Request.php");
});

afterEach(function () {
    File::delete($this->filePath);
});

it('generates correct request validation rules for various Laravel column types', function () {
    $fields = implode(';', [
        'age:unsignedInteger',
        'price:decimal',
        'name:string|max:255',
        'is_active:boolean',
        'birthdate:date',
        'metadata:json',
        'uuid_field:uuid',
        'status:enum',
        'score:float|min:0|max:100',
        'created_by:foreign|constrained:users',
    ]);

    (new GenerateRequestAction)->execute($this->className, $fields);

    expect(File::exists($this->filePath))->toBeTrue();

    $content = File::get($this->filePath);

    expect($content)->toContain("'age' => ['required', 'integer']");
    expect($content)->toContain("'price' => ['required', 'numeric']");
    expect($content)->toContain("'name' => ['required', 'string', 'max:255']");
    expect($content)->toContain("'is_active' => ['required', 'boolean']");
    expect($content)->toContain("'birthdate' => ['required', 'date']");
    expect($content)->toContain("'metadata' => ['required', 'array']");
    expect($content)->toContain("'uuid_field' => ['required', 'string']");
    expect($content)->toContain("'status' => ['required', 'string']");
    expect($content)->toContain("'score' => ['required', 'numeric', 'min:0', 'max:100']");
    expect($content)->toContain("'created_by' => ['required', 'exists:users,id']");

});
