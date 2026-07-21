<?php

namespace Config;

class PostCategories
{
    public static function all(): array
    {
        return [
            'news'     => 'News',
            'events'   => 'Events',
            'features' => 'Features',
        ];
    }

    public static function keys(): array
    {
        return array_keys(static::all());
    }

    public static function label(string $key): string
    {
        return static::all()[$key] ?? ucfirst($key);
    }
}
