<?php

namespace SpykApp\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = ['name', 'label', 'rules', 'classes', 'field_type', 'options', 'sort', 'category', 'model_type', 'model_id'];

    protected $casts = [
        'rules' => 'json',
        'classes' => 'json',
        'options' => 'json',
    ];

    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('custom-fields.tables.custom_fields');
    }

    public function model()
    {
        return $this->morphTo();
    }
}
