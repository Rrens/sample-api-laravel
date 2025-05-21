<?php

namespace App\Models\Utils;

class StoragePath
{
    public static function getStoragePath($path)
    {
        return env('APP_URL') . $path;
    }
}
