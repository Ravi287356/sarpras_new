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
        // inventory details moved to `sarpras_items`
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Auto-generate kode if not provided
            if (empty($model->kode)) {
                $model->kode = $model->generateKode();
            }
        });
    }

    /**
     * Generate unique kode for Sarpras
     */
    public function generateKode()
    {
        // Get kategori for prefix
        $kategori = $this->kategori()->first() ?? KategoriSarpras::find($this->kategori_id);
        
        if (!$kategori) {
            return 'SPR-' . strtoupper(Str::random(6));
        }

        // Create prefix from kategori name (first 3 letters)
        $prefix = strtoupper(substr(str_replace(' ', '', $kategori->nama), 0, 3));
        
        // Get last kode with this prefix
        $lastSarpras = Sarpras::where('kode', 'like', $prefix . '-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(kode, '-', -1) AS UNSIGNED) DESC")
            ->first();
        
        $increment = 1;
        if ($lastSarpras) {
            $parts = explode('-', $lastSarpras->kode);
            if (!empty($parts)) {
                $lastNumber = (int) end($parts);
                $increment = $lastNumber + 1;
            }
        }
        
        return $prefix . '-' . str_pad($increment, 4, '0', STR_PAD_LEFT);
    }


    public function kategori()
    {
        return $this->belongsTo(KategoriSarpras::class, 'kategori_id');
    }

    public function items()
    {
        return $this->hasMany(SarprasItem::class, 'sarpras_id', 'id');
    }

    public function checklists()
    {
        return $this->hasMany(InspectionChecklist::class, 'sarpras_id', 'id');
    }
}
