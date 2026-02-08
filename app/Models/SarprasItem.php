<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SarprasItem extends Model
{
    use HasFactory;

    protected $table = 'sarpras_items';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sarpras_id',
        'kode',
        'lokasi_id',
        'kondisi_alat_id',
        'status_peminjaman_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->kode = $model->generateKode();
        });
    }

    public function generateKode()
    {
        $sarpras = $this->sarpras()->with('kategori')->first();

        if (!$sarpras || !$sarpras->kategori) {
            return 'SPR-' . strtoupper(Str::random(8));
        }

        $namaInisial = strtoupper(substr(str_replace(' ', '', $sarpras->nama), 0, 2));

        $kategoriInisial = strtoupper(substr(str_replace(' ', '', $sarpras->kategori->nama), 0, 2));

        // Use pessimistic locking to prevent race condition
        // Lock the sarpras row to ensure only one process generates kode at a time
        $sarprasyangTerkunci = Sarpras::where('id', $sarpras->id)
            ->lockForUpdate()
            ->first();

        // Ambil kode terbesar yang sudah ada dan extract increment number
        $lastItem = SarprasItem::where('sarpras_id', $sarprasyangTerkunci->id)
            ->whereNull('deleted_at')
            ->orderByRaw("CAST(SUBSTRING_INDEX(kode, '-', -1) AS UNSIGNED) DESC")
            ->select('kode')
            ->first();

        $increment = 1;
        if ($lastItem) {
            // Extract number dari kode, e.g. "MO-EL-001" -> 1
            $parts = explode('-', $lastItem->kode);
            if (!empty($parts)) {
                $lastNumber = (int) end($parts);
                $increment = $lastNumber + 1;
            }
        }

        return "{$namaInisial}-{$kategoriInisial}-" . str_pad($increment, 3, '0', STR_PAD_LEFT);
    }
    public function sarpras()
    {
        return $this->belongsTo(Sarpras::class, 'sarpras_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function kondisi()
    {
        return $this->belongsTo(KondisiAlat::class, 'kondisi_alat_id');
    }

    public function statusPeminjaman()
    {
        return $this->belongsTo(StatusPeminjaman::class, 'status_peminjaman_id');
    }

    /**
     * Get display status based on kondisi and peminjaman status
     *
     * Logika Status:
     * 1. DIPINJAM → status_peminjaman = 'dipinjam'
     * 2. BUTUH MAINTENANCE → kondisi mengandung kata 'rusak'
     * 3. TERSEDIA → status_peminjaman null + kondisi baik
     *
     * Status Peminjaman saat Create/Store:
     * - Semua item dibuat dengan status_peminjaman_id = null
     * - Sistem akan otomatis menentukan display status berdasarkan kondisi
     * - Ketika item dipinjam, status_peminjaman akan berubah ke 'dipinjam'
     * - Ketika item dikembalikan, status_peminjaman kembali ke null atau 'dikembalikan'
     */
    public function peminjamanItems()
    {
        return $this->hasMany(PeminjamanItem::class, 'sarpras_item_id');
    }

    /**
     * Scope to get only available items.
     * Items are available if:
     * 1. Display status is TERSEDIA (includes condition check)
     * 2. Not currently in a pending or active borrowing (menunggu/disetujui)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('status_peminjaman_id')
              ->orWhereHas('statusPeminjaman', function ($sq) {
                  $sq->whereIn('nama', ['tersedia', 'dikembalikan']);
              });
        })
        ->whereHas('kondisi', function ($q) {
            $q->where('nama', 'NOT LIKE', '%hilang%')
              ->where('nama', 'NOT LIKE', '%rusak berat%'); // Allow Rusak Ringan
        })
        ->whereDoesntHave('peminjamanItems', function ($q) {
            $q->whereHas('peminjaman', function ($pq) {
                $pq->whereIn('status', ['menunggu', 'disetujui', 'dipinjam']);
            });
        });
    }

    /**
     * Get display status based on kondisi and peminjaman status
     *
     * Logika Status:
     * 1. DIPINJAM → status_peminjaman = 'dipinjam'
     * 2. MENUNGGU → ada di peminjaman_items dengan status peminjaman 'menunggu'
     * 3. BUTUH MAINTENANCE → kondisi mengandung kata 'rusak'
     * 4. TERSEDIA → status_peminjaman null + kondisi baik
     */
    public function getDisplayStatus()
    {
        // If condition is lost
        if (stripos($this->kondisi?->nama ?? '', 'hilang') !== false) {
            return 'HILANG';
        }

        // If currently being borrowed
        if ($this->statusPeminjaman?->nama === 'dipinjam') {
            return 'DIPINJAM';
        }

        if ($this->statusPeminjaman?->nama === 'Maintenance' || $this->statusPeminjaman?->nama === 'maintenance') {
            return 'MAINTENANCE';
        }

        if ($this->statusPeminjaman?->nama === 'sedang maintenance') {
            return 'SEDANG MAINTENANCE';
        }

        if ($this->statusPeminjaman?->nama === 'butuh maintenance') {
            return 'BUTUH MAINTENANCE';
        }

        $isPending = $this->peminjamanItems()
            ->whereHas('peminjaman', function ($q) {
                $q->where('status', 'menunggu');
            })->exists();

        if ($isPending) {
            return 'DIPESAN'; 
        }

        // If condition is damaged (Only Rusak Berat requires maintenance before borrowing)
        $kondisiNama = $this->kondisi?->nama ?? '';
        if (
            stripos($kondisiNama, 'rusak berat') !== false ||
            stripos($kondisiNama, 'maintenance') !== false
        ) {
            return 'BUTUH MAINTENANCE';
        }

        // Default to available (Includes Baik and Rusak Ringan)
        return 'TERSEDIA';
    }

    /**
     * Get badge color based on display status
     */
    public function getStatusBadgeColor()
    {
        $status = $this->getDisplayStatus();

        return match ($status) {
            'TERSEDIA' => 'emerald',
            'DIPINJAM' => 'amber',
            'DIPESAN' => 'blue',
            'HILANG' => 'rose',
            'BUTUH MAINTENANCE' => 'rose',
            'MAINTENANCE' => 'slate',
            'SEDANG MAINTENANCE' => 'indigo',
            default => 'slate',
        };
    }
}
