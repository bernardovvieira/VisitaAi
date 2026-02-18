<?php

namespace Illuminate\Support\Facades;

abstract class Facade
{
    protected static function getFacadeAccessor(): string
    {
        return '';
    }

    public static function __callStatic(string $method, array $args)
    {
        return null;
    }
}
