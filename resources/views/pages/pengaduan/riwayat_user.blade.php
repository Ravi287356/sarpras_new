@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- Header -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-2">📝 Riwayat Pengaduan Saya</h2>
                <p class="text-muted mb-0">Kelola dan pantau status pengaduan Anda</p>
            </div>
            <a href="{{ route('user.pengaduan.create') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-plus-circle"></i> Buat Pengaduan Baru
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Empty State -->
    @if ($items->count() === 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
                <h5 class="mb-2">Belum Ada Pengaduan</h5>
                <p class="text-muted mb-4">Anda belum membuat pengaduan apapun. Mulai dengan membuat pengaduan jika menemukan masalah pada sarpras.</p>
                <a href="{{ route('user.pengaduan.create') }}" class="btn btn-primary">
                    Buat Pengaduan Pertama
                </a>
            </div>
        </div>
    @else
        <!-- Cards List -->
        <div class="row g-3">
            @forelse($items as $item)
                <div class="col-md-6 col-lg-6">
                    <div class="card h-100 border-0 shadow-sm hover-shadow" style="transition: all 0.3s ease;">
                        <div class="card-body">
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="card-title fw-bold text-truncate mb-1">{{ $item->judul }}</h6>
                                    <small class="text-muted">{{ $item->created_at->format('d M Y, H:i') }}</small>
                                </div>
                                <span class="badge text-white" style="
                                    {{ $item->status === 'Belum Ditindaklanjuti' ? 'background-color: #6c757d;' : '' }}
                                    {{ $item->status === 'Sedang Diproses' ? 'background-color: #ffc107; color: #000;' : '' }}
                                    {{ $item->status === 'Selesai' ? 'background-color: #198754;' : '' }}
                                    {{ $item->status === 'Ditutup' ? 'background-color: #dc3545;' : '' }}
                                ">
                                    {{ $item->status }}
                                </span>
                            </div>

                            <!-- Location -->
                            <div class="mb-3">
                                <small class="text-muted d-block">📍 Lokasi</small>
                                <p class="mb-0">{{ $item->lokasi }}</p>
                            </div>

                            <!-- Description Preview -->
                            <div class="mb-3">
                                <small class="text-muted d-block">📄 Deskripsi</small>
                                <p class="mb-0 text-truncate" style="max-height: 3em; overflow: hidden;">
                                    {{ Str::limit($item->deskripsi, 100) }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                <i class="bi bi-eye"></i> Lihat Detail Lengkap
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Detail -->
                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content border-0 shadow">
                            <!-- Modal Header -->
                            <div class="modal-header bg-light border-bottom">
                                <div>
                                    <h5 class="modal-title fw-bold">{{ $item->judul }}</h5>
                                    <small class="text-muted">Dibuat: {{ $item->created_at->format('d M Y, H:i') }}</small>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <!-- Status -->
                                <div class="mb-4 p-3 rounded" style="background-color: #f8f9fa;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block mb-1">Status Saat Ini</small>
                                            <span class="badge text-white" style="
                                                {{ $item->status === 'Belum Ditindaklanjuti' ? 'background-color: #6c757d;' : '' }}
                                                {{ $item->status === 'Sedang Diproses' ? 'background-color: #ffc107; color: #000;' : '' }}
                                                {{ $item->status === 'Selesai' ? 'background-color: #198754;' : '' }}
                                                {{ $item->status === 'Ditutup' ? 'background-color: #dc3545;' : '' }}
                                            ">
                                                {{ $item->status }}
                                            </span>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <small class="text-muted d-block mb-1">Lokasi</small>
                                            <strong>{{ $item->lokasi }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-2">📋 Deskripsi Masalah</h6>
                                    <p style="white-space: pre-wrap; background-color: #f8f9fa; padding: 1rem; border-radius: 0.5rem; line-height: 1.6;">
                                        {{ $item->deskripsi }}
                                    </p>
                                </div>

                                <!-- Timeline Info -->
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <small class="text-muted d-block">⏰ Timeline</small>
                                        <p class="mb-0 small">
                                            Dibuat: <strong>{{ $item->created_at->format('d M Y') }}</strong>
                                        </p>
                                        @if ($item->updated_at->greaterThan($item->created_at))
                                            <p class="mb-0 small text-muted mt-2">
                                                Update: {{ $item->updated_at->format('d M Y, H:i') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer bg-light border-top">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    @endif
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
