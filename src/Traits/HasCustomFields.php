<?php

namespace SpykApp\LaravelCustomFields\Traits;

use Exception;
use Illuminate\Support\Facades\Validator;
use SpykApp\LaravelCustomFields\Models\CustomField;
use SpykApp\LaravelCustomFields\Models\CustomFieldResponse;

trait HasCustomFields
{
    public function customFields()
    {
        return $this->morphMany(CustomField::class, 'model');
    }

    public function customFieldResponses()
    {
        return $this->morphMany(CustomFieldResponse::class, 'model');
    }

    public function addCustomField(array $fieldData)
    {
        return $this->customFields()->create($fieldData);
    }

    public function updateCustomField($fieldId, array $fieldData)
    {
        return $this->customFields()->where('id', $fieldId)->update($fieldData);
    }

    public function deleteCustomField($fieldId)
    {
        return $this->customFields()->where('id', $fieldId)->delete();
    }

    public function saveCustomFieldResponse($fieldId, $value)
    {
        return $this->customFieldResponses()->updateOrCreate(
            ['custom_field_id' => $fieldId],
            ['value' => $value]
        );
    }

    public function withCustomFields()
    {
        $this->load(['customFields', 'customFieldResponses']);
        return $this;
    }

    public function getCustomFieldsWithResponses()
    {
        $responses = $this->customFieldResponses()->with('customField')->get();

        $fieldsWithResponses = $responses->map(function ($response) {
            $field = $response->customField;
            $field->response = $response;
            return $field;
        });

        return $fieldsWithResponses;
    }


    public function saveCustomFieldResponses(array $responses, $validate = true)
    {
        $savedResponses = [];

        foreach ($responses as $response) {
            $customField = CustomField::where('id', $response['custom_field_id'])
                ->where('model_type', get_class($this))
                ->first();

            if (!$customField) {
                throw new Exception("Custom field with ID {$response['custom_field_id']} does not exist.");
            }

            if ($validate && $customField->rules) {
                $rules = $customField->rules;
                $validator = Validator::make(['value' => $response['value']], ['value' => $rules]);

                if ($validator->fails()) {
                    throw new Exception("Validation failed for custom field '{$customField->name}': " . implode(', ', $validator->errors()->all()));
                }
            }

            $customFieldResponse = CustomFieldResponse::updateOrCreate(
                [
                    'custom_field_id' => $customField->id,
                    'model_type' => get_class($this),
                    'model_id' => $this->getKey(),
                ],
                [
                    'value' => $response['value']
                ]
            );

            $savedResponses[] = $customFieldResponse->id;
        }

        return $savedResponses;
    }
}
