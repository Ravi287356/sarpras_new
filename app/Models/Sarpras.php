<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Helpers\CodeGenerator;

class Sarpras extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sarpras';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'kategori_id',
        'lokasi_id',
        'jumlah_stok',
        'kondisi_saat_ini',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Auto-generate kode jika belum ada atau kosong
            if (empty($model->kode)) {
                $model->kode = CodeGenerator::generate(
                    $model->kategori_id,
                    $model->lokasi_id,
                    $model->nama
                );
            }
        });
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriSarpras::class, 'kategori_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }
}
