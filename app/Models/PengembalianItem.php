<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PengembalianItem extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'pengembalian_items';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pengembalian_id',
        'sarpras_item_id',
        'kondisi_alat_id',
        'foto_url',
        'deskripsi_kerusakan',
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

    public function pengembalian()
    {
        return $this->belongsTo(Pengembalian::class);
    }

    public function sarprasItem()
    {
        return $this->belongsTo(SarprasItem::class);
    }

    public function kondisiAlat()
    {
        return $this->belongsTo(KondisiAlat::class, 'kondisi_alat_id');
    }
}
