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
                        console.log('QR Detected:', decodedText);
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
                    console.error('Scanner Error:', error);
                    alert('Tidak bisa akses kamera. Silakan gunakan upload file atau input manual.');
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
                            alert('QR Code tidak ditemukan di gambar');
                        }
                    };
                    image.src = e.target.result;
                } catch (error) {
                    alert('Error membaca file: ' + error.message);
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
                    window.location.href = '{{ url('/pengembalian') }}/' + result.data.id + '/form';
                } else {
                    alert(result.error || 'Peminjaman tidak ditemukan');
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
    }" class="space-y-6">

        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pengembalian Sarpras</h1>
                <p class="text-sm text-gray-500">Masukkan kode peminjaman atau scan QR untuk memproses pengembalian</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-8">
            <div class="max-w-md mx-auto space-y-6">

                <!-- Input Manual -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3">Kode Peminjaman</label>
                    <div class="space-y-3">
                        <input
                            type="text"
                            x-model="kode_peminjaman"
                            @keyup.enter="searchPeminjaman()"
                            placeholder="Masukkan kode peminjaman..."
                            autofocus
                            class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <button
                            @click="searchPeminjaman()"
                            :disabled="loading"
                            class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:bg-gray-400 transition">
                            <span x-show="!loading">
                                <i class="fa-solid fa-search mr-2"></i>Cari Peminjaman
                            </span>
                            <span x-show="loading">
                                <i class="fa-solid fa-spinner animate-spin mr-2"></i>Mencari...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Scan Options -->
                <div class="border-t pt-6">
                    <p class="text-sm font-bold text-gray-700 mb-3">Atau Scan QR Code</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button
                            @click="initScanner()"
                            type="button"
                            :disabled="loading || showCamera"
                            class="px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 disabled:bg-gray-400 transition text-sm">
                            <i class="fa-solid fa-camera mr-2"></i>Buka Kamera
                        </button>
                        <label class="cursor-pointer">
                            <input
                                type="file"
                                accept="image/*"
                                @change="handleFileUpload"
                                class="hidden"
                            />
                            <div class="px-4 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition text-sm text-center block">
                                <i class="fa-solid fa-image mr-2"></i>Upload Foto
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Camera Preview -->
                <div x-show="showCamera" x-transition class="space-y-3 border-t pt-6">
                    <div class="bg-gray-900 rounded-lg overflow-hidden border-2 border-green-400">
                        <div id="qr-reader" style="width:100%; min-height: 350px;"></div>
                    </div>
                    <button
                        @click="stopScanner()"
                        type="button"
                        class="w-full px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                        <i class="fa-solid fa-stop mr-2"></i>Tutup Kamera
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>

@endsection
