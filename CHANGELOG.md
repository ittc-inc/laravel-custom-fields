# Changelog

All notable changes to `laravel-custom-fields` will be documented in this file.

## [1.0.0] - 25/10/2024
### Added
- Initial release of the `laravel-custom-fields` package.
- Ability to add custom fields to any model using the `HasCustomFields` trait.
- Support for multiple field types (text, select, radio, checkbox, etc.).
- Default value support for custom fields.
- Field validation using Laravel's built-in validation rules.
- Ability to group custom fields by category.
- Multi-tenant support via `entity_id` for saving custom fields per tenant.
- Trait `LoadCustomFields` to dynamically attach custom fields and their responses to model attributes with a configurable prefix.
