@extends('layouts.app')

@section('content')
    <div x-data="{
        kode_peminjaman: '',
        loading: false,
        showCamera: false,
        scannerActive: false,
        qrScanner: null,

        initScanner() {
            if (this.scannerActive) return;

            this.showCamera = true;
            this.$nextTick(() => {
                try {
                    const Html5QrcodeScanner = window.Html5QrcodeScanner;
                    this.qrScanner = new Html5QrcodeScanner(
                        'qr-reader',
                        {
                            fps: 10,
                            qrbox: 250,
                            useBarCodeDetectorIfSupported: true
                        },
                        false
                    );

                    const onScanSuccess = (decodedText) => {
                        this.kode_peminjaman = decodedText.trim();
                        this.stopScanner();
                        setTimeout(() => this.searchPeminjaman(), 300);
                    };

                    const onScanError = (error) => {
                        // Silently ignore scanning errors
                    };

                    this.qrScanner.render(onScanSuccess, onScanError);
                    this.scannerActive = true;
                } catch (error) {
                    alert('Tidak bisa akses kamera.');
                    this.showCamera = false;
                }
            });
        },

        stopScanner() {
            if (this.qrScanner) {
                this.qrScanner.clear();
                this.scannerActive = false;
            }
            this.showCamera = false;
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = async (e) => {
                try {
                    const image = new Image();
                    image.onload = async () => {
                        const canvas = document.createElement('canvas');
                        canvas.width = image.width;
                        canvas.height = image.height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(image, 0, 0);

                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, canvas.width, canvas.height);

                        if (code) {
                            this.kode_peminjaman = code.data.trim();
                            await this.searchPeminjaman();
                        } else {
                            alert('QR Code tidak ditemukan');
                        }
                    };
                    image.src = e.target.result;
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            };
            reader.readAsDataURL(file);
        },

        async searchPeminjaman() {
            if (!this.kode_peminjaman.trim()) {
                alert('Masukkan kode peminjaman');
                return;
            }

            this.loading = true;
            try {
                const response = await fetch('{{ route('pengembalian.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        kode_peminjaman: this.kode_peminjaman
                    })
                });

                const result = await response.json();

                if (result.success) {
                    window.location.href = '{{ url('/pengembalian') }}/' + result.data.id;
                } else {
                    alert(result.error || 'Data tidak ditemukan');
                    this.kode_peminjaman = '';
                    this.showCamera = false;
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
                this.kode_peminjaman = '';
            } finally {
                this.loading = false;
            }
        }
    }" class="min-h-[80vh] flex flex-col items-center justify-center p-6">

        <div class="w-full max-w-md bg-slate-900 border border-white/10 rounded-2xl shadow-xl overflow-hidden">
            <!-- Header -->
            <div class="bg-slate-800 p-6 text-center border-b border-white/5">
                <div class="mx-auto w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-box-open text-2xl text-white"></i>
                </div>
                <h1 class="text-xl font-bold text-white">Pengembalian Barang</h1>
                <p class="text-sm text-slate-400 mt-1">Scan QR atau input manual kode peminjaman</p>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6">
                
                <!-- Input Manual -->
                <div>
                     <label class="block text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wide">Input Kode Manual</label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            x-model="kode_peminjaman"
                            @keyup.enter="searchPeminjaman()"
                            placeholder="Contoh: PMJ-ABC123"
                            class="flex-1 px-4 py-3 bg-slate-950 border border-white/10 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        />
                        <button
                            @click="searchPeminjaman()"
                            :disabled="loading"
                            class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg disabled:opacity-50 transition">
                             <i class="fa-solid fa-search" x-show="!loading"></i>
                             <i class="fa-solid fa-spinner animate-spin" x-show="loading"></i>
                        </button>
                    </div>
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-white/10"></div>
                    <span class="flex-shrink-0 mx-4 text-slate-500 text-xs">ATAU</span>
                    <div class="flex-grow border-t border-white/10"></div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-4">
                    <button
                        @click="initScanner()"
                        type="button"
                        :disabled="loading || showCamera"
                        class="flex flex-col items-center justify-center gap-2 p-4 bg-slate-800 hover:bg-slate-700 border border-white/5 rounded-xl transition group">
                        <i class="fa-solid fa-qrcode text-2xl text-emerald-400"></i>
                        <span class="text-sm font-medium text-slate-300">Scan QR Code</span>
                    </button>

                    <label class="flex flex-col items-center justify-center gap-2 p-4 bg-slate-800 hover:bg-slate-700 border border-white/5 rounded-xl transition cursor-pointer group">
                        <input type="file" accept="image/*" @change="handleFileUpload" class="hidden" />
                        <i class="fa-solid fa-image text-2xl text-purple-400"></i>
                        <span class="text-sm font-medium text-slate-300">Upload Foto</span>
                    </label>
                </div>

                <!-- Camera Preview -->
                <div x-show="showCamera" class="bg-black rounded-lg overflow-hidden border border-white/10">
                    <div id="qr-reader" style="width:100%;"></div>
                    <button
                        @click="stopScanner()"
                        type="button"
                        class="w-full py-3 bg-red-600 text-white font-semibold text-sm hover:bg-red-700 text-center">
                        Tutup Kamera
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
@endsection
