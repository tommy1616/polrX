<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (! function_exists('str_limit')) {
    function str_limit(?string $value, int $limit = 100, string $end = '...'): string
    {
        return Str::limit((string) $value, $limit, $end);
    }
}

if (! function_exists('str_random')) {
    function str_random(int $length = 16): string
    {
        return Str::random($length);
    }
}

if (! function_exists('array_get')) {
    function array_get(mixed $array, string|int|null $key, mixed $default = null): mixed
    {
        return Arr::get($array, $key, $default);
    }
}
