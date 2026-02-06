<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lokasi;
use App\Models\KategoriSarpras;
use App\Models\CatatanPengaduan;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'pengaduan';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'lokasi_id',
        'kategori_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriSarpras::class, 'kategori_id', 'id');
    }

    public function catatanPengaduan()
    {
        return $this->hasMany(CatatanPengaduan::class, 'pengaduan_id', 'id');
    }
}
