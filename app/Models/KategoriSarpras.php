<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KategoriSarpras extends Model
{
    use SoftDeletes;

    protected $table = 'kategori_sarpras';

    public $incrementing = false;
    protected $keyType = 'string';

    // ✅ pakai default Laravel
    const DELETED_AT = 'deleted_at';

    protected $fillable = [
        'id',
        'nama',
        'deskripsi',
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
