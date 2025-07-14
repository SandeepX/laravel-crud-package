<?php

namespace MrIncognito\CrudGenerator\Actions\Web;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AppendWebRoutesAction
{
    public function execute(string $name): void
    {
        $resource = Str::kebab(Str::pluralStudly($name));
        File::append(base_path('routes/web.php'), "\nRoute::resource('{$resource}', \\App\\Http\\Controllers\\Web\\{$name}Controller::class);");
    }
}
