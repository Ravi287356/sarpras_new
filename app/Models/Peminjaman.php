<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'peminjaman';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sarpras()
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id');
    }

    // admin/operator yang menyetujui
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
