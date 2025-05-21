<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model
{
    use HasFactory, SoftDeletes;

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

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

    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
}
