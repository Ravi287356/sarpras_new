@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa;">
    <!-- Header -->
    <div class="container mb-5">
        <h2 class="fw-bold mb-2">📊 Data Pengaduan</h2>
        <p class="text-muted mb-0">Kelola dan perbarui status pengaduan dari semua pengguna</p>
    </div>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="container mb-4">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if ($items->count() === 0)
        <div class="container">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                    <h5 class="mb-2">Tidak Ada Pengaduan</h5>
                    <p class="text-muted mb-0">Saat ini belum ada pengaduan yang masuk.</p>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <!-- Stats Cards -->
            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div style="font-size: 2rem; color: #dc3545; margin-bottom: 0.5rem;">📬</div>
                            <h6 class="text-muted mb-2">Total Pengaduan</h6>
                            <h4 class="fw-bold">{{ $items->total() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div style="font-size: 2rem; color: #6c757d; margin-bottom: 0.5rem;">⏳</div>
                            <h6 class="text-muted mb-2">Belum Diproses</h6>
                            <h4 class="fw-bold">{{ $items->where('status', 'Belum Ditindaklanjuti')->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div style="font-size: 2rem; color: #ffc107; margin-bottom: 0.5rem;">🔄</div>
                            <h6 class="text-muted mb-2">Sedang Diproses</h6>
                            <h4 class="fw-bold">{{ $items->where('status', 'Sedang Diproses')->count() }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body">
                            <div style="font-size: 2rem; color: #198754; margin-bottom: 0.5rem;">✅</div>
                            <h6 class="text-muted mb-2">Selesai</h6>
                            <h4 class="fw-bold">{{ $items->where('status', 'Selesai')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th>Pelapor</th>
                                <th>Judul</th>
                                <th>Lokasi</th>
                                <th class="text-center">Status</th>
                                <th class="text-end px-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $index => $item)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3 text-muted small">{{ ($items->currentPage() - 1) * $items->perPage() + $index + 1 }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $item->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $item->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $item->judul }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $item->created_at->format('d M Y, H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>📍 {{ $item->lokasi }}</td>
                                    <td class="text-center">
                                        <span class="badge text-white" style="
                                            {{ $item->status === 'Belum Ditindaklanjuti' ? 'background-color: #6c757d;' : '' }}
                                            {{ $item->status === 'Sedang Diproses' ? 'background-color: #ffc107; color: #000;' : '' }}
                                            {{ $item->status === 'Selesai' ? 'background-color: #198754;' : '' }}
                                            {{ $item->status === 'Ditutup' ? 'background-color: #dc3545;' : '' }}
                                        ">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateModal{{ $item->id }}">
                                            <i class="bi bi-pencil-square"></i> Update
                                        </button>
                                    </td>
                                </tr>

                                <!-- Update Modal -->
                                <div class="modal fade" id="updateModal{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-light border-bottom">
                                                <h5 class="modal-title fw-bold">Update Status Pengaduan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form action="{{ route(auth()->user()->role->nama.'.pengaduan.updateStatus', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">
                                                    <!-- Item Info -->
                                                    <div class="card bg-light border-0 mb-3">
                                                        <div class="card-body small">
                                                            <strong>{{ $item->judul }}</strong>
                                                            <br>
                                                            <span class="text-muted">{{ $item->lokasi }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Status Select -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Status Baru</label>
                                                        <select name="status" class="form-select form-select-lg" required>
                                                            <option value="">-- Pilih Status --</option>
                                                            <option value="Belum Ditindaklanjuti" {{ $item->status === 'Belum Ditindaklanjuti' ? 'selected' : '' }}>
                                                                ⏳ Belum Ditindaklanjuti
                                                            </option>
                                                            <option value="Sedang Diproses" {{ $item->status === 'Sedang Diproses' ? 'selected' : '' }}>
                                                                🔄 Sedang Diproses
                                                            </option>
                                                            <option value="Selesai" {{ $item->status === 'Selesai' ? 'selected' : '' }}>
                                                                ✅ Selesai
                                                            </option>
                                                            <option value="Ditutup" {{ $item->status === 'Ditutup' ? 'selected' : '' }}>
                                                                ❌ Ditutup
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <!-- Catatan -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Catatan (Opsional)</label>
                                                        <textarea name="catatan" class="form-control" rows="3" 
                                                                  placeholder="Tulis catatan atau tindakan yang telah diambil..."></textarea>
                                                        <small class="text-muted d-block mt-2">Catatan akan ditambahkan ke deskripsi pengaduan</small>
                                                    </div>
                                                </div>

                                                <div class="modal-footer bg-light border-top">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $items->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
