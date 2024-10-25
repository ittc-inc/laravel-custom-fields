<?php

namespace SpykApp\LaravelCustomFields\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = [
        'name',
        'label',
        'placeholder',
        'rules',
        'classes',
        'field_type',
        'options',
        'default_value',
        'description',
        'hint',
        'sort',
        'category',
        'model_type',
        'entity_id'
    ];

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

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('sort', 'asc')->orderBy('id', 'asc');
        });
    }

    public function model()
    {
        return $this->morphTo();
    }
}
