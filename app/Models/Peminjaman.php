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
        // items moved to peminjaman_items
        'tujuan',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_actual',
        'status',
        'status_peminjaman_id',
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

    /**
     * Accessor for 'sarpras' to maintain backward compatibility with views.
     * Retrieving the sarpras from the first item.
     * Note: This assumes all items in a peminjaman belong to the same sarpras (which is enforcing in current controller logic).
     */
    public function getSarprasAttribute()
    {
        return $this->items->first()?->sarprasItem?->sarpras;
    }

    /**
     * Accessor for 'jumlah' to maintain backward compatibility.
     */
    public function getJumlahAttribute()
    {
        return $this->items->count();
    }

    public function items()
    {
        return $this->hasMany(PeminjamanItem::class, 'peminjaman_id', 'id');
    }

    public function statusPeminjaman()
    {
        return $this->belongsTo(StatusPeminjaman::class, 'status_peminjaman_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }
}
