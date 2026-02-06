<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatatanPengaduan extends Model
{
use SoftDeletes;

    use HasFactory;

    protected $table = 'catatan_pengaduan';

    protected $fillable = [
        'pengaduan_id',
        'user_id',
        'catatan',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
