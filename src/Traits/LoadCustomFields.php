<?php
namespace SpykApp\LaravelCustomFields\Traits;

use Illuminate\Database\Eloquent\Collection;

trait LoadCustomFields
{

    public static function bootLoadCustomFields()
    {
        static::retrieved(function ($model) {
            $model->loadCustomFields();
        });
    }

    public function newCollection(array $models = [])
    {
        foreach ($models as $model) {
            $model->loadCustomFields();
        }

        return new Collection($models);
    }

    public function loadCustomFields()
    {
        $prefix = config('custom-fields.field_prefix', 'cf_');

        $responses = $this->customFieldResponses()->with('customField')->get();

        foreach ($responses as $response) {
            $field = $response->customField;
            $fieldName = $prefix . $field->name;
            $this->attributes[$fieldName] = $response->value ?? null;
        }
    }


}
