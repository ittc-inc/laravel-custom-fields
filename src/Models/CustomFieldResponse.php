<?php

namespace SpykApp\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomFieldResponse extends Model
{
    protected $fillable = ['custom_field_id', 'model_type', 'model_id', 'value'];

    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('custom-fields.tables.custom_field_responses');
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
