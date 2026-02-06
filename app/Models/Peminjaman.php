<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    use SoftDeletes;
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
        'tanggal_kembali_rencana',
        'tanggal_kembali_actual',
        'status',
        'approved_by',
        'approved_at',
        'alasan_penolakan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->kode_peminjaman = 'PMJ-' . strtoupper(Str::random(6));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sarpras()
    {
        return $this->belongsTo(Sarpras::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
