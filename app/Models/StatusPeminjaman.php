<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPeminjaman extends Model
{
    protected $table = 'status_peminjaman';
    protected $fillable = ['nama'];
}
