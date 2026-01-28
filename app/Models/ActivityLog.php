<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    use SoftDeletes;

    protected $table = 'activity_logs';

    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'timesatamp'; // ✅ sesuai DB
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'user_id',
        'aksi',
        'deskripsi',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) $model->id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
