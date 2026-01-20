<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriSarpras extends Model
{
    use SoftDeletes;

    protected $table = 'kategori_sarpras';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];
}
