<?php

namespace MrIncognito\CrudGenerator\Tests\Unit;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/** Example usage:
 * php artisan make:crud Comment --fields="post_id:foreignId;user_id:foreignId~"
 *
 * Explanation:
 *
 * post_id:foreignId;
 * Creates a post_id field as a foreign ID (unsigned big integer), required by default, with a foreign key constraint (->constrained()).
 *
 * user_id:foreignId~
 * Creates a user_id field as a foreign ID, but nullable (due to the ~), meaning it’s optional. It also adds the foreign key constraint (->constrained()).
 */
beforeEach(function () {
    $this->modelName = 'Comment';

    $this->migrationPath = collect(File::files(database_path('migrations')))
        ->map(fn ($f) => $f->getPathname());
});

afterEach(function () {
    File::delete(app_path("Models/{$this->modelName}.php"));

    foreach (File::files(database_path('migrations')) as $file) {
        if (Str::contains($file->getFilename(), 'create_comments_table')) {
            File::delete($file->getPathname());
        }
    }
});

it('generates foreignId fields with correct nullability and constraints', function () {
    $fields = 'post_id:foreignId;user_id:foreignId~';

    $this->artisan("make:crud {$this->modelName} --fields={$fields}")
        ->assertExitCode(0)
        ->run();

    $migrationFiles = File::files(database_path('migrations'));
    $migrationFile = collect($migrationFiles)->first(fn ($file) => Str::contains($file->getFilename(), 'create_comments_table')
    );

    expect($migrationFile)->not->toBeNull();

    $content = File::get($migrationFile->getPathname());

    expect($content)->toContain('$table->foreignId(\'post_id\');');
    expect($content)->toContain('$table->foreignId(\'user_id\')->nullable();');
});
