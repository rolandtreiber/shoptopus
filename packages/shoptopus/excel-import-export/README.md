# Import and Export

## What this package does

This package has the following capabilities:

- Exports data from the system through models and relationships into Excel spreadsheet file
- Exports template files to be used for importing data into Excel spreadsheet file
- Validates import Excel spreadsheet files and provides detailed report whether the file has any invalid data
- Imports data into the system through import Excel spreadsheet files

## How it works

The module reads the model information through the ReflectionClass and ReflectionMethod classes. It scans the models for their fillable, exportable and importable fields and relationships and either creates the exported data or template spreadsheets or evaluates the uploaded file.
When exporting, the module includes one sheet per model.
> **A note on slugs**\
> The module expects the model to have a slug field.\
> The reason is that relationships are represented by a comma separated list of slugs or the related models.
> The recommended package for slug support is [Laravel Sluggable](https://github.com/spatie/laravel-sluggable).

### Configuration

Publish the configuration by the ```php artisan vendor:publish``` command. If you are to be presented with a list,
select the corresponding number of ```Provider: Shoptopus\ExcelImportExport\ExcelImportExportServiceProvider```. Then
locate ```config/excel_import_export.php``` to access the configurations.

#### Initial values

```
return [
    'middleware' => '',
    'prefix' => '',
    'ignored_relationships' => [],
    'model_namespace' => 'App\\Models',
    'languages' => ['en']
];
```

##### Middleware

The middleware applied on the routes provided by the package.\
Example`'middleware' => ['role:super_admin']`

##### Prefix

The prefix applied to the routes. Example `'prefix' => 'io'` would result the following route to be
valid: `[YOUR_APPLICATION_URL]/io/import`

##### Ignored relationships

Relationships that should not be exported under any circumstances, such as audits.
Example `'ignored_relationships' => ['audits'],`

##### Model Namespace

The namespace of the model classes in your application Example `'model_namespace' => 'App\\Models'`

##### Languages

The list of available languages in your system provided
through [Laravel Translatabable](https://github.com/spatie/laravel-translatable) package.\
Example `'languages' => ['en', 'fr', 'de']`

### Routes

The following routes are available through the package

#### Export

`[GET] [APP_URL]/[PREFIX]/export`

| Parameter | Type   | Example  | Description           |
|-----------|--------|----------|-----------------------|
| name      | string | products | File name             |
| models[]  | array  | Product  | Models to be exported |

Produces a downloadable Excel spreadsheet file.

#### Export Template

`[GET] [APP_URL]/[PREFIX]/template`

| Parameter | Type   | Example  | Description                                            |
|-----------|--------|----------|--------------------------------------------------------|
| name      | string | products | File name ( - TEMPLATE will be automatically appended) |
| model     | string | Product  | Model name                                             |

Produces a downloadable Excel spreadsheet file.

#### Validate Uploaded file

`[POST] [APP_URL]/[PREFIX]/validate`

| Parameter | Type                                            | Example      | Description       |
|-----------|-------------------------------------------------|--------------|-------------------|
| file      | uploaded Excel spreadsheet file (.xls or .xlsx) | products.xls | The uploaded file |

Returns a detailed evaluation of the uploaded file.

Sample response:

```{
    "message": "success",
    "details": [
        {
            "valid": true,
            "data": [
                {
                    "field": "name",
                    "value": {
                        "en": "value",
                        "fr": "value",
                        "de": "value"
                    },
                    "raw_value": "en: value; fr: value; de: value; ",
                    "translatable": true,
                    "valid": true
                },
                {
                    "field": "value",
                    "value": "value",
                    "raw_value": "value",
                    "valid": true
                },
                {
                    "field": "enabled",
                    "value": 1,
                    "raw_value": 1,
                    "valid": true
                },
                {
                    "relationship": "product_attribute",
                    "type": "BelongsTo",
                    "table": "product_attributes",
                    "value": [
                        "sed"
                    ],
                    "raw_value": "sed",
                    "valid": true
                }
            ]
        }
    ]
}
```

#### Import

`[POST] [APP_URL]/[PREFIX]/import`

| Parameter | Type                                            | Example      | Description       |
|-----------|-------------------------------------------------|--------------|-------------------|
| file      | uploaded Excel spreadsheet file (.xls or .xlsx) | products.xls | The uploaded file |

Sample success response:

```
{
    "status": "success",
    "message": "1 records have been successfully imported."
}
```

Sample error response:

```
{
    "status": "invalid data",
    "message": "The file you have uploaded has invalid data.",
    "details": [
        {
            "valid": false,
            "data": [
                {
                    "field": "name",
                    "value": {
                        "en": "Product",
                        "fr": "Produit",
                        "de": "Produkt"
                    },
                    "raw_value": "en: Product; fr: Produit; de: Produkt; ",
                    "translatable": true,
                    "valid": true
                },
                {
                    "field": "short_description",
                    "value": {
                        "fr": "Petit description",
                        "de": "Kurze Beschreibung"
                    },
                    "raw_value": "Sp: Short description; fr: Petit description; de: Kurze Beschreibung; ",
                    "translatable": true,
                    "valid": false,
                    "errors": [
                        "No such language code: Sp"
                    ]
                },
                {
                    "field": "description",
                    "value": {
                        "en": "Description",
                        "fr": "Description",
                        "de": "Beschreibung"
                    },
                    "raw_value": "en: Description; fr: Description; de: Beschreibung; ",
                    "translatable": true,
                    "valid": true
                },
                {
                    "field": "price",
                    "value": 12,
                    "raw_value": 12,
                    "valid": true
                },
                {
                    "field": "status",
                    "value": 2,
                    "raw_value": 2,
                    "valid": true
                },
                {
                    "field": "stock",
                    "value": 22,
                    "raw_value": 22,
                    "valid": true
                },
                {
                    "field": "backup_stock",
                    "value": 23,
                    "raw_value": 23,
                    "valid": true
                },
                {
                    "field": "sku",
                    "value": "TEST-0001",
                    "raw_value": "TEST-0001",
                    "valid": false,
                    "errors": [
                        "The sku has already been taken."
                    ]
                },
                {
                    "relationship": "discount_rules",
                    "type": "BelongsToMany",
                    "table": "discount_rules",
                    "model": "App\\Models\\DiscountRule",
                    "value": [
                        "ps2-off",
                        "10-off"
                    ],
                    "raw_value": "ps2-off, 10-off",
                    "valid": true
                },
                {
                    "relationship": "product_categories",
                    "type": "BelongsToMany",
                    "table": "product_categories",
                    "model": "App\\Models\\ProductCategory",
                    "value": [],
                    "raw_value": null,
                    "valid": true
                },
                {
                    "relationship": "product_tags",
                    "type": "BelongsToMany",
                    "table": "product_tags",
                    "model": "App\\Models\\ProductTag",
                    "value": [],
                    "raw_value": null,
                    "valid": true
                },
                {
                    "relationship": "product_attributes",
                    "type": "BelongsToMany",
                    "table": "product_attributes",
                    "model": "App\\Models\\ProductAttribute",
                    "value": [],
                    "raw_value": null,
                    "valid": true
                }
            ]
        },
        {
            "valid": false,
            "data": [
                {
                    "field": "name",
                    "value": {
                        "en": "Product 2",
                        "fr": "Produit 2",
                        "de": "Produkt 2"
                    },
                    "raw_value": "en: Product 2; fr: Produit 2; de: Produkt 2; ",
                    "translatable": true,
                    "valid": true
                },
                {
                    "field": "short_description",
                    "value": {
                        "en": "Short description",
                        "fr": "Petit description",
                        "de": "Kurze Beschreibung"
                    },
                    "raw_value": "en: Short description; fr: Petit description; de: Kurze Beschreibung; ",
                    "translatable": true,
                    "valid": true
                },
                {
                    "field": "description",
                    "value": {
                        "en": "Description",
                        "fr": "Description",
                        "de": "Beschreibung"
                    },
                    "raw_value": "en: Description; fr: Description; de: Beschreibung; ",
                    "translatable": true,
                    "valid": true
                },
                {
                    "field": "price",
                    "value": 22,
                    "raw_value": 22,
                    "valid": true
                },
                {
                    "field": "status",
                    "value": 1,
                    "raw_value": 1,
                    "valid": true
                },
                {
                    "field": "stock",
                    "value": 14,
                    "raw_value": 14,
                    "valid": true
                },
                {
                    "field": "backup_stock",
                    "value": 233,
                    "raw_value": 233,
                    "valid": true
                },
                {
                    "field": "sku",
                    "value": "TEST-0002",
                    "raw_value": "TEST-0002",
                    "valid": false,
                    "errors": [
                        "The sku has already been taken."
                    ]
                },
                {
                    "relationship": "discount_rules",
                    "type": "BelongsToMany",
                    "table": "discount_rules",
                    "model": "App\\Models\\DiscountRule",
                    "value": [],
                    "raw_value": null,
                    "valid": true
                },
                {
                    "relationship": "product_categories",
                    "type": "BelongsToMany",
                    "table": "product_categories",
                    "model": "App\\Models\\ProductCategory",
                    "value": [],
                    "raw_value": null,
                    "valid": true
                },
                {
                    "relationship": "product_tags",
                    "type": "BelongsToMany",
                    "table": "product_tags",
                    "model": "App\\Models\\ProductTag",
                    "value": [],
                    "raw_value": null,
                    "valid": true
                },
                {
                    "relationship": "product_attributes",
                    "type": "BelongsToMany",
                    "table": "product_attributes",
                    "model": "App\\Models\\ProductAttribute",
                    "value": [],
                    "raw_value": null,
                    "valid": true
                }
            ]
        }
    ]
}
```

## Requirements

### Export

- The model needs to implement the ```Shoptopus\ExcelImportExport\Exportable```
- The model needs to use the ```Shoptopus\ExcelImportExport\traits\HasExportable``` trait
- Add the ```protected $exportableFields = []``` array to the model class.
- Add the ```protected $exportableRelationships = []``` array to the model class

#### $exportableFields

Simple array of strings of fields that are intended to be exported.

#### $exportableRelationships

Simple array of strings of relationships that are intended to be exported.

### Import

- The model needs to implement the ```Shoptopus\ExcelImportExport\Importable```
- The model needs to use the ```Shoptopus\ExcelImportExport\traits\HasImportable``` trait
- Add the ```protected $importableFields = []``` array to the model class.
- Add the ```protected $importableRelationships = []``` array to the model class

#### $importableFields

Associative array of fields that are intended to be imported. Please note that this array can optionally contain
configuration.\

Example:
```    
protected $importableFields = [
   'name',
   'short_description',
   'description',
   'price' => [
       'validation' => ['numeric']
   ],
   'status' => [
       'description' => '1 = Provisional, 2 = Active, 3 = Discontinued',
       'validation' => ['integer', 'min:1', 'max:3']
   ]
];
```
> The validation array holds the rules to validate the field in the spreadsheet.
> It uses Laravel's Validator class, therefore all built-in validation rules are available.

#### $importableRelationships
Simple array of strings of relationships that are intended to be imported.
