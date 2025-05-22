<?php

namespace App\Models\Pgsql;

use App\Models\PersonalAccessTokenPgsql;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

class UserPgsql extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'users';

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function mail()
    {
        return $this->belongsTo(MailPgsql::class);
    }

    public static function generateTokenFor(UserPgsql $user, string $name = 'authToken')
    {
        $tokenModel = \App\Models\PersonalAccessTokenPgsql::class;

        $plainText = Str::random(40);
        $tokenId = Str::uuid()->toString();

        $token = $tokenModel::create([
            'id' => $tokenId,
            'tokenable_type' => get_class($user),
            'tokenable_id' => $user->getKey(),
            'name' => $name,
            'token' => hash('sha256', $plainText),
            'abilities' => ['*'],
        ]);

        return new NewAccessToken($token, $plainText);
    }
}
