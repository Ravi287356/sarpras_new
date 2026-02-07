<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KondisiAlat extends Model
{
	protected $table = 'kondisi_alat';
	protected $fillable = ['nama'];
}
