<?php

namespace SpykApp\LaravelCustomFields\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SpykApp\LaravelCustomFields\LaravelCustomFields
 */
class LaravelCustomFields extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SpykApp\LaravelCustomFields\LaravelCustomFields::class;
    }
}
