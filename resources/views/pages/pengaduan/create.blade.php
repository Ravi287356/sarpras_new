@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-5">
                <h2 class="fw-bold mb-2">📋 Ajukan Pengaduan</h2>
                <p class="text-muted">Laporkan masalah atau kerusakan pada sarpras yang Anda temukan</p>
            </div>

            <!-- Error Alert -->
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6 class="alert-heading">⚠️ Ada Kesalahan</h6>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Form Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('user.pengaduan.store') }}" method="POST" novalidate>
                        @csrf

                        <!-- Judul Field -->
                        <div class="mb-4">
                            <label for="judul" class="form-label fw-semibold">Judul Pengaduan <span class="text-danger">*</span></label>
                            <input type="text" id="judul" name="judul" 
                                   class="form-control form-control-lg @error('judul') is-invalid @enderror" 
                                   placeholder="Contoh: Projector di ruang 101 tidak berfungsi" 
                                   required value="{{ old('judul') }}">
                            <small class="text-muted d-block mt-2">Jelaskan judul masalah secara singkat dan jelas</small>
                            @error('judul')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi Field -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi Masalah <span class="text-danger">*</span></label>
                            <textarea id="deskripsi" name="deskripsi" 
                                      class="form-control @error('deskripsi') is-invalid @enderror" 
                                      rows="6" placeholder="Jelaskan masalah secara detail:&#10;- Apa yang terjadi?&#10;- Kapan masalah terjadi?&#10;- Dampak dari masalah ini" 
                                      required>{{ old('deskripsi') }}</textarea>
                            <small class="text-muted d-block mt-2">Tuliskan deskripsi sedetail mungkin agar petugas dapat menangani dengan baik</small>
                            @error('deskripsi')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lokasi Field -->
                        <div class="mb-4">
                            <label for="lokasi" class="form-label fw-semibold">Lokasi Barang <span class="text-danger">*</span></label>
                            <input type="text" id="lokasi" name="lokasi" 
                                   class="form-control form-control-lg @error('lokasi') is-invalid @enderror" 
                                   placeholder="Contoh: Ruang 101, Lantai 2" 
                                   required value="{{ old('lokasi') }}">
                            <small class="text-muted d-block mt-2">Sebutkan lokasi barang yang bermasalah</small>
                            @error('lokasi')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="bi bi-check-circle"></i> Kirim Pengaduan
                            </button>
                            <a href="{{ route('user.pengaduan.riwayat') }}" class="btn btn-outline-secondary btn-lg">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info-light border-start border-info border-4 mt-4" style="background-color: #e7f3ff;">
                <p class="mb-0 small">
                    <strong>💡 Tips:</strong> Cantumkan informasi sedetail mungkin agar petugas dapat merespons dengan cepat dan akurat.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
