<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Pengembalian extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'pengembalian';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'peminjaman_id',
        'tanggal_pengembalian',
        'kondisi_alat',
        'deskripsi_kerusakan',
        'foto_url',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }
}
