<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inspections';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sarpras_item_id',
        'user_id',
        'peminjaman_id',
        'tipe_inspeksi',
        'tanggal_inspeksi',
        'catatan_umum',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class);
    }

    protected $casts = [
        'tanggal_inspeksi' => 'datetime',
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

    public function item()
    {
        return $this->belongsTo(SarprasItem::class, 'sarpras_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function results()
    {
        return $this->hasMany(InspectionResult::class);
    }
}
