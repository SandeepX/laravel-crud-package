# Laravel API CRUD Generator

A Laravel package to quickly scaffold API-based CRUD operations using a simple artisan command. Generates Models, Migrations, Form Requests, API Resources, and Controllers with proper validation rules and fillable fields — with support for foreign keys, nullable fields, and default values.

## Installation

To install the package, run:

```bash
composer require mr.incognito/crudify
```

---

## ✨ Features

- ✅ Generates **Model**, **Migration**, **Controller**, **Request**, and **Resource**
- ✅ Handles **nullable** fields (using `~` as a suffix)
- ✅ Supports **foreign key constraints**
- ✅ Adds **default values** to migration fields
- ✅ Type-aware validation rules (e.g., `string`, `integer`, `boolean`)
- ✅ Artisan-based generation: fast and developer-friendly
- ✅ Built-in Pest tests

---

🚀 Usage

```

php artisan make:api-crud ModelName --fields="field:type|rule1,rule2;another:foreign~|constrained:table"

```
## Example 1:
To create crud without foreignKey

```
php artisan make:api-crud Department --fields="name:string|max:255;created_by:foreign~|constrained:users"

```

## Example 2:
To create crud with required foreignKey column and with constrained

```

php artisan make:api-crud Department --fields="name:string;created_by:foreign|constrained:users|onDelete:cascade"

```

## Example 3:
To create crud with nullable foreignKey column 

```

php artisan make:api-crud Department --fields="name:string;created_by:foreign~|constrained:hospitals

```


## Example 4:
To create crud with default value  column 

```

php artisan make:api-crud Department --fields="name:string;status:boolean~|default:true"

```



##  These all will Generate

- app/Models/Department.php

- app/Http/Controllers/DepartmentController.php

- app/Http/Requests/DepartmentRequest.php

- app/Http/Resources/DepartmentResource.php

- database/migrations/xxxx_xx_xx_create_departments_table.php

- Adds route in routes/api.php

## Field Syntax
Each field uses the format:
```
column_one_name:data_type[~]|rules|default:xyz;column_two_name:foreignId[~]|constrained:table|

```

## Supported Field Types
You can use any of the following Laravel migration column types:

string, text, boolean, integer, decimal, date, uuid, json, timestamp, etc.

Foreign key via foreign, e.g., user_id:foreign~|constrained:users

Nullable fields: suffix type with ~, e.g., email:string~

Default values: default:value, e.g., status:boolean|default:true

## column Modifiers
- ``` ~ :``` Makes the field nullable
- ``` default:value :``` Sets a default value in the migration 
- ``` constrained :``` Adds a foreign key constraint
- ``` onDelete:CASCADE :``` Adds delete behavior for foreign keys

## Testing

This package uses Pest for testing:
```
composer test
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
