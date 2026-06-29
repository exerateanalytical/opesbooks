@extends('admin.layout')

@section('title', 'Error Logs')

@section('content')
<div class="mb-8 flex items-center justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-black text-white uppercase tracking-wide">{{ $showAll ? 'Application Logs' : 'Error Logs' }}</h1>
        <p class="text-slate-500 text-xs mt-1">
            {{ $showAll ? 'All levels' : 'Errors & warnings only' }} — tail of laravel.log
            @if($truncated)<span class="text-amber-400">· showing last 256&nbsp;KB</span>@endif
        </p>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-1 bg-[#1C2A3A] border border-[#334155] rounded-xl p-1">
            <a href="{{ route('admin.logs') }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ ! $showAll ? 'bg-red-500/20 text-red-300' : 'text-slate-400 hover:text-white' }}">Errors</a>
            <a href="{{ route('admin.logs', ['level' => 'all']) }}" class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $showAll ? 'bg-amber-500/20 text-amber-300' : 'text-slate-400 hover:text-white' }}">All</a>
        </div>
        <span class="text-[10px] font-mono font-black px-3 py-1 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">
            {{ count($entries) }} entries
        </span>
    </div>
</div>

<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-[#0B1120]/50">
                    <th class="py-3 px-6">Time</th>
                    <th class="py-3 px-4">Level</th>
                    <th class="py-3 px-4">Message</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($entries as $e)
                    @php
                        $lvl = $e['level'];
                        $color = in_array($lvl, ['ERROR','CRITICAL','ALERT','EMERGENCY'])
                            ? 'bg-red-500/20 text-red-300 border-red-500/30'
                            : ($lvl === 'WARNING' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30'
                            : 'bg-slate-500/20 text-slate-300 border-slate-500/30');
                    @endphp
                    <tr class="hover:bg-[#1C2A3A]/40 transition-colors">
                        <td class="py-3 px-6 font-mono text-[10px] text-slate-500 whitespace-nowrap">{{ $e['ts'] }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border {{ $color }}">{{ $lvl }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-300 font-mono text-[11px]">{{ $e['message'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-12 text-center text-slate-500 text-sm">No log entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
