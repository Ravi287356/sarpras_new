<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kode_peminjaman',
        'user_id',
        'sarpras_id',
        'jumlah',
        'tujuan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'approved_by',
        'approved_at',
        'alasan_penolakan',
    ];

    // ✅ FIX BOOT
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }

            if (!$model->kode_peminjaman) {
                $model->kode_peminjaman = 'PMJ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // ✅ FIX RELASI USER (INI KUNCI)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // ✅ SARPRAS
    public function sarpras()
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id', 'id');
    }

    // ✅ APPROVER (ADMIN / OPERATOR)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
