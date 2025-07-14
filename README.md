# Laravel CRUD Generator

A Laravel package to quickly scaffold **API or Web-based CRUD operations** using a simple Artisan command. It generates **Models**, **Migrations**, **Form Requests**, **API Resources**, **Controllers**, and even **Blade views** — with full support for validation, foreign keys, nullable fields, and default values.

## Installation

To install the package, run:

```bash
composer require mr.incognito/crudify
```

---

## ✨ Features

- ✅ Generates **Model**, **Migration**, **Controller**, **Request**, and **Resource**
- ✅ Supports Web (with Blade views) or API type generation
- ✅ Handles **nullable** fields (using `~` as a suffix)
- ✅ Supports **foreign key constraints**
- ✅ Adds **default values** to migration fields
- ✅ Type-aware validation rules (e.g., `string`, `integer`, `boolean`)
- ✅ Artisan-based generation: fast and developer-friendly
- ✅ **--type** flag to specific crud type api or web based( with default is api)
- ✅ **--exclude** flag to skip generating specific files
- ✅ Built-in Pest tests
- ✅ Code refactoring via Rector

---

🚀 Usage

```
php artisan make:crud ModelName --fields="field:type|rule1,rule2;another:foreign~|constrained:table" --type=api|web --exclude=model,..
```
## Example 1:
To create crud without foreignKey

```
php artisan make:crud Department --fields="name:string|max:255;created_by:foreign~|constrained:users"
```

## Example 2:
To create crud with required foreignKey column and with constrained

```
php artisan make:crud Department --fields="name:string;created_by:foreign|constrained:users|onDelete:cascade"
```

## Example 3:
To create crud with nullable foreignKey column 

```

php artisan make:crud Department --fields="name:string;created_by:foreign~|constrained:hospitals"
```


## Example 4:
To create crud with default value  column 

```
php artisan make:crud Department --fields="name:string;status:boolean~|default:true"
```

##  These all will Generate

- app/Models/Department.php

- app/Http/Controllers/DepartmentController.php

- app/Http/Requests/DepartmentRequest.php

- app/Http/Resources/DepartmentResource.php

- database/migrations/xxxx_xx_xx_create_departments_table.php

- Adds route in routes/api.php

### Example 6:
Exclude Model and Migration
```
php artisan make:crud Department --fields="name:string" --type=api --exclude=model,migration
```
Skips model and migration, still creates controller, request, and resource.

## Field Syntax
Each field uses the format:
```
column_one_name:data_type[~]|rules|default:xyz;column_two_name:foreignId[~]|constrained:table

```

## Supported Field Types
You can use any of the following Laravel migration column types:

string, text, boolean, integer, decimal, date, uuid, json, timestamp, etc.

Foreign key via foreign, e.g., user_id:foreign~|constrained:users

Nullable fields: suffix type with `~`, e.g., email:string~

Default values: default:value, e.g., status:boolean|default:true

## column Modifiers
- ``` ~ :``` Makes the field nullable
- ``` default:value :``` Sets a default value in the migration 
- ``` constrained :``` Adds a foreign key constraint
- ``` onDelete:CASCADE :``` Adds delete behavior for foreign keys


### 🆕 New in v2
🎯 ``--type=api``: generates API controller and resource only

🎯 ``--type=web``: generates web controller and Blade views

✂️ ``--exclude``=model,migration,request,...: skip generating specific components

### ⚠️ Default Behavior
If `--type` is not specified, the command defaults to type=api.
```
php artisan make:crud Book --fields="title:string;author:string" 
```
This is equivalent to:
```
php artisan make:crud Book --fields="title:string;author:string" --type=api
```
By default, it generates API-related files:
- API Controller

- API Resource

- Form Request

- Model

- Migration

- Adds route to api.php

### Example 5:
Web CRUD with Blade Views

```
php artisan make:crud Article --fields="title:string;content:text" --type=web
```

### Generates:
- Model, Migration
- Blade Views: resources/views/articles/*.blade.php
- Web\ArticleController
- Route in web.php

### Example 5:
Web CRUD with Blade Views

```
php artisan make:crud Article --fields="title:string;content:text" --type=web --exclude=migration,model
```

### Generates only:
- Blade Views: resources/views/articles/*.blade.php
- Web\ArticleController
- Route in web.php

## Testing

This package uses Pest for testing:
```
composer test
```

## Refactoring

This package uses [Rector](https://github.com/rectorphp/rector) for automated code refactoring and PHP/Laravel upgrades:

```bash
composer rector
```



## 🛠 Dev Requirements
- PHP ^8.1

- Laravel ^10 or ^11 or ^12

- PestPHP for testing

- Laravel pint for code style fixer 

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## 🧑 Author

Sandeep Pant

📧 sandeeppant024@gmail.com

## License
This package is open-sourced software licensed under the MIT License.

[MIT](https://choosealicense.com/licenses/mit/)
