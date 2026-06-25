@extends('admin.layout')

@section('title', 'System Health')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">System Health</h1>
    <p class="text-slate-500 text-xs mt-1">Platform status & diagnostics</p>
</div>

@php
    $degraded = $failedJobs > 0;
    foreach ($services as $svc) {
        if (empty($svc['ok'])) { $degraded = true; break; }
    }
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Service status card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Service Status</span>
            @if($degraded)
                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-red-500/20 text-red-300 border border-red-500/30">Degraded</span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Operational</span>
            @endif
        </div>
        <div class="divide-y divide-slate-800/60">
            @foreach($services as $name => $svc)
                <div class="flex items-center justify-between py-3">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full {{ !empty($svc['ok']) ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                        <span class="font-bold text-white text-sm">{{ $name }}</span>
                    </div>
                    <span class="text-slate-400 text-xs text-right">{{ $svc['detail'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Failed jobs card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Queue Health</span>
        <div class="mt-6 flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full {{ $failedJobs > 0 ? 'bg-red-400' : 'bg-emerald-400' }}"></span>
            @if($failedJobs > 0)
                <span class="text-red-400 text-lg font-black">{{ number_format($failedJobs) }} failed jobs</span>
            @else
                <span class="text-emerald-400 text-lg font-black">0 failed jobs</span>
            @endif
        </div>
        <p class="text-slate-500 text-xs mt-2">Background queue worker status across all tenants.</p>
        <div class="flex gap-2 mt-4">
            <form method="POST" action="{{ route('admin.system.retry-jobs') }}" onsubmit="return confirm('Relancer tous les jobs échoués ?')">
                @csrf
                <button class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wide bg-amber-500/15 text-amber-300 border border-amber-500/30 hover:bg-amber-500/25">Relancer les jobs échoués</button>
            </form>
            <form method="POST" action="{{ route('admin.system.flush-jobs') }}" onsubmit="return confirm('Vider définitivement la file des jobs échoués ?')">
                @csrf
                <button class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wide bg-red-500/15 text-red-300 border border-red-500/30 hover:bg-red-500/25">Vider la file</button>
            </form>
        </div>
    </div>
</div>

<!-- Platform counts -->
<div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
    <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Platform Counts</span>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5">
        <div class="bg-slate-950/40 border border-slate-800 rounded-xl p-4">
            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Companies</div>
            <div class="text-white text-2xl font-black mt-2">{{ number_format($counts['companies']) }}</div>
        </div>
        <div class="bg-slate-950/40 border border-slate-800 rounded-xl p-4">
            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Users</div>
            <div class="text-white text-2xl font-black mt-2">{{ number_format($counts['users']) }}</div>
        </div>
        <div class="bg-slate-950/40 border border-slate-800 rounded-xl p-4">
            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Active API Keys</div>
            <div class="text-white text-2xl font-black mt-2">{{ number_format($counts['api_keys']) }}</div>
        </div>
        <div class="bg-slate-950/40 border border-slate-800 rounded-xl p-4">
            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">API Calls 24h</div>
            <div class="text-white text-2xl font-black mt-2">{{ number_format($counts['api_calls_24h']) }}</div>
        </div>
    </div>
</div>
@endsection
