# 📋 Dokumentasi Struk Peminjaman & QR Code

## 📝 Yang Telah Dibuat

### 1. **View Struk Peminjaman** (`resources/views/pages/peminjaman/struk.blade.php`)
   - Menampilkan struk peminjaman profesional dalam format cetak
   - Berisi:
     - Kode Peminjaman dengan QR Code
     - Informasi Peminjam (Username, Nama, Email, Role)
     - Informasi Penyetuju (Nama Admin/Operator & Tanggal Persetujuan)
     - Detail Sarpras (Nama, Kode, Kategori, Lokasi)
     - Detail Peminjaman (Jumlah, Tanggal Pinjam, Estimasi Kembali)
     - Tujuan Peminjaman
     - Status Peminjaman
   - **Fitur Cetak**: Tombol print yang menyembunyikan toolbar saat dicetak
   - **QR Code**: Menggunakan library `simplesoftwareio/simple-qrcode`

### 2. **Method Controller** (`app/Http/Controllers/PeminjamanController.php`)
   - **`struk(string $id)`**: Method untuk menampilkan struk
     - Generate QR code dari kode_peminjaman
     - Proteksi akses: Hanya user yang membuat peminjaman atau admin/operator
     - Hanya dapat menampilkan struk jika status = 'disetujui' atau 'dikembalikan'

### 3. **Routes**
   - **Admin**: `GET /admin/peminjaman/{id}/struk` → `admin.peminjaman.struk`
   - **Operator**: `GET /operator/peminjaman/{id}/struk` → `operator.peminjaman.struk`
   - **User**: `GET /user/peminjaman/{id}/struk` → `user.peminjaman.struk`

### 4. **Update View Peminjaman Aktif** (`resources/views/pages/peminjaman/aktif.blade.php`)
   - Menambahkan kolom Aksi dengan dua tombol:
     - 📄 **Struk**: Menampilkan struk peminjaman
     - ✓ **Kembalikan**: Menandai peminjaman sebagai dikembalikan

### 5. **Update View Riwayat Peminjaman** (`resources/views/pages/peminjaman/riwayat.blade.php`)
   - Menambahkan kolom Aksi
   - Tombol "Struk" hanya muncul untuk peminjaman dengan status 'disetujui' atau 'dikembalikan'

## 🔒 Keamanan
- Hanya admin/operator atau user yang membuat peminjaman dapat melihat struk
- Struk hanya dapat ditampilkan untuk peminjaman yang sudah disetujui
- Authorization check di method controller

## 🎨 Styling
- Menggunakan Tailwind CSS
- Design responsif dan print-friendly
- QR Code ukuran 200x200 px
- Layout clean dan profesional

## 📦 Dependencies
- `simplesoftwareio/simple-qrcode`: ^4.2 (sudah tersedia)

## 🚀 Cara Menggunakan

### Dari Halaman Peminjaman Aktif (Admin/Operator)
1. Buka halaman "Peminjaman Aktif"
2. Klik tombol "📄 Struk" pada baris peminjaman
3. Struk akan ditampilkan dengan QR Code
4. Klik "🖨️ Cetak" untuk mencetak struk

### Dari Halaman Riwayat Peminjaman (User)
1. Buka halaman "Riwayat Peminjaman"
2. Untuk peminjaman yang sudah disetujui/dikembalikan, klik "📄 Struk"
3. Struk akan ditampilkan dengan QR Code
4. Klik "🖨️ Cetak" untuk mencetak struk

## 📊 Data yang Ditampilkan di Struk

| Bagian | Data |
|--------|------|
| Header | STRUK PEMINJAMAN |
| Kode & QR | Kode Peminjaman + QR Code |
| Peminjam | Username, Nama, Email, Role |
| Penyetuju | Nama & Tanggal Persetujuan |
| Sarpras | Nama, Kode, Kategori, Lokasi |
| Peminjaman | Jumlah, Tanggal Pinjam, Est. Kembali |
| Tujuan | Tujuan Peminjaman (jika ada) |
| Status | Status Peminjaman |
| Footer | Info Cetak & Waktu Generate |

## 🔧 Maintenance

### QR Code Value
QR Code berisi: **Kode Peminjaman** (e.g., "PMJ-20260130-ABC123")

### Jika Ingin Mengubah Format QR Code
Edit di `PeminjamanController.php`:
```php
$qrCode = QrCode::size(200)->generate($peminjaman->kode_peminjaman);
```

Opsi kustomisasi:
- `size(300)`: Ukuran QR Code
- `format('png')`: Format output
- `color(rgb)`: Warna kode

---
✅ **Status**: SELESAI - Siap digunakan dalam production
