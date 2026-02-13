<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InspectionChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inspection_checklists';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sarpras_id',
        'tujuan_periksa',
        'is_aktif',
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

    public function sarpras()
    {
        return $this->belongsTo(Sarpras::class);
    }
}
