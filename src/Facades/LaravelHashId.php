<?php

namespace Bretterer\LaravelHashId\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bretterer\LaravelHashId\LaravelHashId
 */
class LaravelHashId extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Bretterer\LaravelHashId\LaravelHashId::class;
    }
}
