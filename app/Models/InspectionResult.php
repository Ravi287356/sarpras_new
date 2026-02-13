<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InspectionResult extends Model
{
    use HasFactory;

    protected $table = 'inspection_results';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'inspection_id',
        'inspection_checklist_id',
        'status',
        'catatan',
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

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function checklist()
    {
        return $this->belongsTo(InspectionChecklist::class, 'inspection_checklist_id');
    }
}
