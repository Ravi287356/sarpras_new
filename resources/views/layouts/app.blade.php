<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Sarpras' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body class="bg-slate-950 text-white overflow-hidden" x-data="{ sidebarOpen: false }">

<div class="h-screen flex relative">

    {{-- BACKDROP (Mobile Only) --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 lg:hidden">
    </div>

    {{-- SIDEBAR --}}
    <aside class="w-72 fixed inset-y-0 left-0 bg-slate-950 z-[60] transform transition-transform duration-300 ease-in-out lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <x-sidebar :menus="$menus" :panelTitle="$panelTitle" />
    </aside>

    {{-- CONTENT --}}
    <div class="flex-1 flex flex-col min-w-0 h-screen lg:ml-72">

        {{-- HEADER --}}
        <header class="shrink-0 border-b border-white/10 bg-slate-950/80 backdrop-blur z-40">
            <div class="flex items-center justify-between px-6 py-4">

                <div class="flex items-center gap-4">
                    {{-- Hamburger Menu (Mobile Only) --}}
                    <button @click="sidebarOpen = true" class="lg:hidden text-white/70 hover:text-white transition">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>

                    <div class="text-sm text-slate-300">
                        Login sebagai
                        <b class="text-white">{{ auth()->user()->username }}</b>
                        <span class="text-emerald-300">• {{ auth()->user()->role->nama }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ url('/' . $roleName . '/profile') }}"
                       class="px-4 py-2 rounded-xl text-sm font-medium
                              ring-1 ring-white/10 hover:bg-white/5 transition">
                        <i class="fa-solid fa-user"></i> Profil
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 rounded-xl text-sm font-medium
                                       bg-red-600/20 text-red-200 ring-1 ring-red-500/30
                                       hover:bg-red-600/30 transition">
                            Logout
                        </button>
                    </form>
                </div>

            </div>
        </header>

        {{-- MAIN CONTENT (YANG SCROLL) --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
        </main>

    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swalConfig = {
            showClass: { popup: '', backdrop: '' },
            hideClass: { popup: '', backdrop: '' },
            background: '#0f172a',
            color: '#f8fafc',
            buttonsStyling: true,
            reverseButtons: true,
            backdrop: 'rgba(2, 6, 23, 0.6)',
            width: '24rem',
            padding: '1.5rem',
            customClass: {
                popup: 'rounded-xl border border-white/10 shadow-lg',
                confirmButton: 'rounded-lg px-6 py-2 text-sm font-semibold',
                cancelButton: 'rounded-lg px-6 py-2 text-sm font-semibold'
            }
        };

        // Flash Messages (Fast & Auto-dismiss)
        @if(session('success'))
            Swal.fire({
                ...swalConfig,
                title: 'Berhasil',
                text: "{{ session('success') }}",
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                ...swalConfig,
                title: 'Gagal',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#ef4444'
            });
        @endif

        // Global Confirmation
        document.addEventListener('submit', function(e) {
            const form = e.target;
            
            // Skip already confirmed or GET forms or logout
            if (form.dataset.swalOk === 'true' || form.method.toLowerCase() === 'get' || form.action.includes('logout')) {
                return;
            }

            e.preventDefault();

            const submitBtn = e.submitter;
            const btnText = submitBtn ? submitBtn.innerText.trim() : 'Simpan';
            const actionLower = btnText.toLowerCase();
            
            let color = '#3b82f6';
            if (actionLower.includes('hapus') || actionLower.includes('tolak')) color = '#ef4444';
            if (actionLower.includes('tambah') || actionLower.includes('setujui') || actionLower.includes('selesai')) color = '#10b981';

            Swal.fire({
                ...swalConfig,
                title: btnText + '?',
                text: 'Lanjutkan tindakan ini?',
                showCancelButton: true,
                confirmButtonText: btnText,
                cancelButtonText: 'Batal',
                confirmButtonColor: color,
                cancelButtonColor: '#334155',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.swalOk = 'true';
                    // Trigger native submission more robustly
                    const newSubmitter = document.createElement('input');
                    newSubmitter.type = 'hidden';
                    newSubmitter.name = submitBtn?.name || '_submit_confirmed';
                    newSubmitter.value = submitBtn?.value || 'true';
                    form.appendChild(newSubmitter);
                    
                    form.submit();
                }
            });
        });
    });
</script>

<style>
    /* Absolute reset for SweetAlert2 movement */
    .swal2-container, .swal2-popup, .swal2-backdrop-show, .swal2-backdrop-hide {
        transition: none !important;
        animation: none !important;
    }
    div:where(.swal2-container) div:where(.swal2-popup) {
        animation: none !important;
    }
</style>
</body>
</html>
