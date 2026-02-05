<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Sarpras' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body class="bg-slate-950 text-white overflow-hidden">

<div class="h-screen flex">

    {{-- SIDEBAR (FIXED, TANPA SCROLLBAR) --}}
    <aside class="w-72 fixed inset-y-0 left-0 bg-slate-950">
        <x-sidebar :menus="$menus" :panelTitle="$panelTitle" />
    </aside>

    {{-- CONTENT --}}
    <div class="flex-1 flex flex-col ml-72 h-screen">

        {{-- HEADER --}}
        <header class="shrink-0 border-b border-white/10 bg-slate-950/80 backdrop-blur z-40">
            <div class="flex items-center justify-between px-6 py-4">

                <div class="text-sm text-slate-300">
                    Login sebagai
                    <b class="text-white">{{ auth()->user()->username }}</b>
                    <span class="text-emerald-300">• {{ auth()->user()->role->nama }}</span>
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
</body>
</html>
