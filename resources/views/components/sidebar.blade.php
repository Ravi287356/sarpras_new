<style>
    /* ===============================
       HIDE SCROLLBAR (SCROLL TETAP ADA)
       =============================== */
    .hide-scrollbar {
        -ms-overflow-style: none;
        /* IE & Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .hide-scrollbar::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari */
    }
</style>

<div
    class="fixed left-0 top-0 w-72 h-screen
            bg-gradient-to-b from-slate-950 to-slate-900
            border-r border-white/10
            flex flex-col">

    {{-- HEADER --}}
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <div>
            <div class="text-xl font-bold tracking-wide text-white">SARPRAS</div>
            <div class="text-xs text-slate-400 mt-1 uppercase tracking-wider font-semibold">
                {{ $panelTitle ?? 'Dashboard' }}
            </div>
        </div>
        
        {{-- Close Button (Mobile Only) --}}
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white transition">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    @php
        $role = auth()->user()->role->nama ?? 'guest';
        $menus = $menus ?? [];

        $pengaduanMenu = $role === 'user'
            ? [
                'label' => 'Pengaduan',
                'children' => [
                    [
                        'label' => 'Buat Pengaduan',
                        'route' => route('user.pengaduan.create'),
                        'active' => 'user/pengaduan/create',
                    ],
                    [
                        'label' => 'Riwayat Pengaduan',
                        'route' => route('user.pengaduan.riwayat'),
                        'active' => 'user/pengaduan/riwayat',
                    ],
                ],
            ]
            : [
                'label' => 'Pengaduan',
                'route' => route($role . '.pengaduan.index'),
                'active' => $role . '/pengaduan*',
            ];

        $exists = false;
        foreach ($menus as $m) {
            if (isset($m['label']) && $m['label'] === 'Pengaduan') {
                $exists = true;
                break;
            }
        }
        if (! $exists) {
            $menus[] = $pengaduanMenu;
        }
    @endphp

    {{-- MENU --}}
    <nav id="sidebar-nav" class="p-3 space-y-2 flex-1 overflow-y-auto hide-scrollbar">
        @foreach ($menus as $key => $menu)
            @php
                $menuHref = $menu['route'] ?? '#';
                $isActiveMenu = isset($menu['active']) ? request()->is($menu['active']) : false;

                $isActiveChild = false;
                if (isset($menu['children'])) {
                    foreach ($menu['children'] as $c) {
                        if (isset($c['active']) && request()->is($c['active'])) {
                            $isActiveChild = true;
                            break;
                        }
                    }
                }
            @endphp

            {{-- MENU DENGAN SUBMENU --}}
            @if (isset($menu['children']))
                <div x-data="{
                    open: {{ $isActiveChild ? 'true' : "localStorage.getItem('menu_$key') === 'true'" }},
                    toggle() {
                        this.open = !this.open
                        localStorage.setItem('menu_{{ $key }}', this.open)
                    }
                }" class="space-y-1">
                    <button type="button" @click="toggle"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm
                               border border-white/10 hover:bg-white/5 transition
                               {{ $isActiveMenu || $isActiveChild
                                   ? 'bg-emerald-500/10 border-emerald-400/30 text-emerald-200'
                                   : 'text-slate-200' }}">
                        <span class="font-medium">{{ $menu['label'] }}</span>
                        <span class="text-xs transition" :class="open ? 'rotate-180' : ''">▼</span>
                    </button>

                    <div x-show="open" x-transition class="pl-3 space-y-1">
                        @foreach ($menu['children'] as $child)
                            @php
                                $childHref = $child['route'] ?? '#';
                                $isActiveThisChild = isset($child['active']) ? request()->is($child['active']) : false;
                            @endphp

                            <a href="{{ $childHref }}"
                                class="block px-4 py-2 rounded-xl text-sm
                                      border border-transparent hover:bg-white/5 transition
                                      {{ $isActiveThisChild ? 'bg-emerald-500/10 text-emerald-200 border-emerald-400/30' : 'text-slate-300' }}">
                                • {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- MENU TANPA SUBMENU --}}
            @else
                <a href="{{ $menuHref }}"
                    class="block px-4 py-3 rounded-xl text-sm
                          border border-white/10 hover:bg-white/5 transition
                          {{ $isActiveMenu ? 'bg-emerald-500/10 border-emerald-400/30 text-emerald-200' : 'text-slate-200' }}">
                    <span class="font-medium">{{ $menu['label'] }}</span>
                </a>
            @endif

        @endforeach
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar-nav');
        const pos = localStorage.getItem('sidebar-scroll');

        if (pos) {
            sidebar.scrollTop = pos;
        }

        // Simpan posisi scroll saat user klik link di sidebar atau reload
        window.addEventListener('beforeunload', () => {
            localStorage.setItem('sidebar-scroll', sidebar.scrollTop);
        });
    });
</script>
