<?php

namespace App\Models\Pgsql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MailPgsql extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    protected $table = 'mails';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'subject',
        'user_id',
        'body',
        'sender_id',
        // 'file_url',
        'blob_file',
        'file_extention',
        'mime_type',
        'original_name',
        'mail_type',
        'is_read',
        'recheivedAt',
    ];

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    public function receiver()
    {
        return $this->belongsTo(UserPgsql::class, 'user_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(UserPgsql::class, 'sender_id', 'id');
    }
}
