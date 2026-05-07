<?php

namespace App\Helpers;

class StoragePath
{
    public static function cachedResult(string $filename = ''): string
    {
        return storage_path('app/cached_result/' . $filename);
    }

    public static function signature(string $filename = ''): string
    {
        return storage_path('app/signatures/' . $filename);
    }
}