@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
    <div class="mb-5">
        <h1 class="text-2xl font-semibold tracking-tight">Activity Log</h1>
        <p class="text-slate-400 text-sm mt-1">Mencatat waktu, user, role, dan aksi yang dilakukan.</p>
    </div>

    <div class="rounded-2xl bg-slate-900/40 ring-1 ring-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-left w-44">Waktu</th>
                        <th class="px-5 py-4 text-left">User</th>
                        <th class="px-5 py-4 text-left w-28">Role</th>
                        <th class="px-5 py-4 text-left w-32">Aksi</th>
                        <th class="px-5 py-4 text-left">Aktivitas & IP</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-white/10">
                    @forelse($logs as $log)
                        <tr class="text-slate-100 hover:bg-white/[0.02] transition">
                            <td class="px-5 py-4 text-slate-300">
                                {{ optional($log->timestamp)->format('d-m-Y H:i:s') }}
                            </td>

                            <td class="px-5 py-4 font-medium">
                                {{ $log->user?->username ?? '-' }}
                            </td>

                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full text-xs ring-1 bg-white/5 ring-white/10">
                                    {{ $log->user?->role?->nama ?? '-' }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold ring-1 
                                    @if($log->aksi == 'LOGIN_GAGAL') bg-red-500/10 text-red-200 ring-red-500/30 
                                    @elseif($log->aksi == 'LOGIN') bg-emerald-500/10 text-emerald-200 ring-emerald-500/30
                                    @else bg-blue-500/10 text-blue-200 ring-blue-500/30 @endif lowercase tracking-wider">
                                    {{ str_replace('_', ' ', strtolower($log->aksi)) }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-slate-200 font-medium">{{ $log->deskripsi ?? '-' }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-mono text-slate-500 flex items-center gap-1">
                                            <i class="bi bi-pc-display"></i> {{ $log->ip_address ?? '?.?.?.?' }}
                                        </span>
                                        @if(isset($log->metadata['user_agent']))
                                            <span class="text-[10px] text-slate-600 truncate max-w-[200px]" title="{{ $log->metadata['user_agent'] }}">
                                                {{ Str::limit($log->metadata['user_agent'], 40) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                Belum ada activity log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-white/10 bg-white/[0.02]">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

