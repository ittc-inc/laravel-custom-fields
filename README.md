# Laravel Package for Managing Custom Fields for Each Model

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spykapp/laravel-custom-fields.svg?style=flat-square)](https://packagist.org/packages/spykapp/laravel-custom-fields)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spykapp/laravel-custom-fields/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spykapp/laravel-custom-fields/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spykapp/laravel-custom-fields/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spykapp/laravel-custom-fields/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spykapp/laravel-custom-fields.svg?style=flat-square)](https://packagist.org/packages/spykapp/laravel-custom-fields)

This Laravel package allows you to dynamically add custom fields to any model in your application. It supports storing custom fields as polymorphic relationships and offers dynamic loading of custom field responses directly into the model’s attributes, including support for default values.

## Installation

You can install the package via composer:

```bash
composer require spykapp/laravel-custom-fields
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="custom-fields-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="custom-fields-config"
```

This is the contents of the published config file:

```php
return [
    'tables' => [
        'custom_fields' => 'custom_fields',
        'custom_field_responses' => 'custom_field_responses',
    ],
    'field_prefix' => 'cf_', // Custom prefix for custom field attributes
];
```

## Usage
### Configuration

You can customize the table names and field prefixes by publishing the configuration file and making changes to it.

### Overriding Models
You may want to extend or override the `CustomField` or `CustomFieldResponse` models provided by this package. To do this, you can publish the configuration file and update it to use your own custom models.

### Trait for Managing Custom Fields
To use this package, add the `HasCustomFields` trait to any model where you want to allow custom fields. This trait allows you to easily add, update, and retrieve custom fields and their responses.
```php
use SpykApp\LaravelCustomFields\Traits\HasCustomFields;

class Post extends Model
{
    use HasCustomFields;

    // Your model code here...
}
```
Now you can add custom fields for this model, save responses, and retrieve them as attributes with a configurable prefix.
### Add Custom Fields
You can create a custom field for the model like this:

```php
use App\Models\Post;
use SpykApp\LaravelCustomFields\Models\CustomField;

CustomField::create([
    'name' => 'subtitle',
    'label' => 'Subtitle',
    'field_type' => 'text',
    'default_value' => 'Default Subtitle',
    'model_type' => Post::class,
]);
```
Below is a list of fields used in the `CustomField` model and their purposes:

- **`name`**: The unique identifier for the custom field. This is used as the reference name for accessing the custom field's value.

- **`label`**: The human-readable label for the field, which will be displayed in the forms.

- **`placeholder`**: Placeholder text that will appear inside input fields (useful for text inputs).

- **`rules`**: Laravel validation rules for the field. You can define validation rules like `'required'`, `'email'`, etc., to validate user input. This field should be stored in JSON format.

- **`classes`**: Additional CSS classes to apply to the field's HTML element. This allows for custom styling of each field.

- **`field_type`**: Specifies the type of the field (e.g., `text`, `textarea`, `select`, `radio`, `checkbox`). This determines what kind of input element will be generated in the form.

- **`options`**: This is used specifically for fields like `select`, `radio`, or `checkbox` to store the available options. It is stored as JSON in the database. Example: `{"options": ["Option 1", "Option 2", "Option 3"]}`.

- **`default_value`**: The default value that will be used if no response is provided by the user. This is useful for setting a fallback value.

- **`description`**: A longer description that explains the purpose or use of the field. This can be shown as part of the form to provide context to the user.

- **`hint`**: A hint or tooltip that can be displayed next to the field to help users understand what the field is for.

- **`sort`**: This is an integer used to order the fields. Fields with lower `sort` values will be displayed before fields with higher `sort` values.

- **`category`**: This allows you to group custom fields into categories. For instance, you might have custom fields categorized as `personal_info` and others as `job_details`. When rendering the form, fields can be grouped by their category.

- **`model_type`**: This is the model class name that the custom field applies to. For example, `App\Models\Post` if the custom field is attached to the `Post` model.

- **`entity_id`**: This is used for multi-tenant applications. Each tenant can have their own set of custom fields by specifying a unique `entity_id`. This ensures that custom fields are isolated per tenant.

### Save Custom Field Responses
You can save responses for custom fields like this:

The `saveCustomFieldResponses` method is used to either **insert** or **update** responses for custom fields tied to a specific model record. This ensures that if a response already exists for a given field on a record, it will be updated, and if not, a new response will be inserted.

```php
$post = Post::find(1);
$post->saveCustomFieldResponses([
  [
    'custom_field_id' => 1,
    'value' => 'This is the subtitle',
  ],
  [
    'custom_field_id' => 2,
    // No value provided, so it will use default_value
  ]
], validate: false);
```
You may pass `validate : false` as a second parameter to bypass the validations which is ON by default.

### Loading Custom Fields with Responses
You can simply load the custom field responses by calling the model with relationships like this:
```php
Post::with(['customFieldResponses.customField'])->get();
```
This will retrieve all `Post` records along with their custom field responses and the related custom fields.

You can also fetch a single post with its custom fields and responses like this:
```php
Post::with(['customFieldResponses.customField'])->find(1);
```

#### Using Helper Function

For a single Post, you can use the helper function `getCustomFieldsWithResponses()` to load the custom fields and their responses:
```php
$post = Post::find(1);
$post->getCustomFieldsWithResponses();
```
This will load the custom fields and their associated responses for the given post instance, making them easily accessible.

#### Using the `LoadCustomFields` Trait
Alternatively, you can use our second trait, `LoadCustomFields`, which automatically attaches the custom fields and their responses as attributes to each model instance.

Custom field keys will be prefixed by a string that you can configure in the configuration file. By default, the prefix is cf_, but you can change it in the config.

```php
use SpykApp\LaravelCustomFields\Traits\HasCustomFields;
use SpykApp\LaravelCustomFields\Traits\LoadCustomFields;

class Post extends Model
{
    use HasCustomFields, LoadCustomFields;

    // Your model code here...
}
```
Using the LoadCustomFields trait, you can access custom fields directly as attributes:

```php
$post = Post::find(1);
echo $post->cf_subtitle; // Outputs the value of the 'subtitle' custom field, or null if not set
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Credits

- [Sanchit Patil](https://github.com/SpykApp)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
