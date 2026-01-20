<div class="fixed left-0 top-0 w-72 h-screen bg-gradient-to-b from-slate-950 to-slate-900 border-r border-white/10 flex flex-col">
    <div class="p-6 border-b border-white/10">
        <div class="text-xl font-bold tracking-wide">SARPRAS</div>
        <div class="text-sm text-slate-400 mt-1">{{ $panelTitle ?? 'Dashboard' }}</div>
    </div>

    <nav class="p-3 space-y-2 overflow-y-auto flex-1">
        @foreach ($menus as $key => $menu)

            @php
                $menuHref = isset($menu['route_name'])
                    ? route($menu['route_name'])
                    : (isset($menu['route']) ? url($menu['route']) : '#');
            @endphp

            @if (isset($menu['children']))
                <div x-data="{
                        open: localStorage.getItem('menu_{{ $key }}') === 'true',
                        toggle() {
                            this.open = !this.open
                            localStorage.setItem('menu_{{ $key }}', this.open)
                        }
                    }" class="space-y-1">

                    <button type="button" @click="toggle"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-sm
                               border border-white/10 hover:bg-white/5 transition
                               {{ request()->is($menu['active']) ? 'bg-emerald-500/10 border-emerald-400/30 text-emerald-200' : 'text-slate-200' }}">
                        <span class="font-medium">{{ $menu['label'] }}</span>
                        <span class="text-xs transition" :class="open ? 'rotate-180' : ''">▼</span>
                    </button>

                    <div x-show="open" x-transition class="pl-3 space-y-1">
                        @foreach ($menu['children'] as $child)

                            @php
                                $childHref = isset($child['route_name'])
                                    ? route($child['route_name'])
                                    : (isset($child['route']) ? url($child['route']) : '#');
                            @endphp

                            <a href="{{ $childHref }}"
                               class="block px-4 py-2 rounded-xl text-sm border border-transparent
                                      hover:bg-white/5 transition
                                      {{ request()->is($child['active']) ? 'bg-emerald-500/10 text-emerald-200 border-emerald-400/30' : 'text-slate-300' }}">
                                • {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <a href="{{ $menuHref }}"
                   class="block px-4 py-3 rounded-xl text-sm border border-white/10 hover:bg-white/5 transition
                          {{ request()->is($menu['active']) ? 'bg-emerald-500/10 border-emerald-400/30 text-emerald-200' : 'text-slate-200' }}">
                    <span class="font-medium">{{ $menu['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>
</div>
