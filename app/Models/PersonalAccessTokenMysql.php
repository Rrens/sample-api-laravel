<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessTokenMysql extends SanctumPersonalAccessToken
{
    protected $connection = 'mysql';
    protected $table = 'personal_access_tokens';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'token',
        'tokenable_type',
        'tokenable_id',
        'abilities',
        'updated_at',
        'created_at',
    ];
}
