<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Lokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lokasi';

    public $incrementing = false;
    protected $keyType = 'string';

    // ✅ pastikan soft delete pakai kolom default Laravel
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'id',
        'nama',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
