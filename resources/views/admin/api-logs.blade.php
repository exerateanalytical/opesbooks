@extends('admin.layout')

@section('title', 'API Request Logs')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">API Request Logs</h1>
    <p class="text-slate-500 text-xs mt-1">Full observability of API usage</p>
</div>

<!-- Stat strip -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Total Requests</div>
        <div class="text-2xl font-black text-white mt-2">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Today</div>
        <div class="text-2xl font-black text-amber-400 mt-2">{{ number_format($stats['today']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Errors (24h)</div>
        <div class="text-2xl font-black text-red-400 mt-2">{{ number_format($stats['errors_24h']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Avg Latency</div>
        <div class="text-2xl font-black text-white mt-2">{{ $stats['avg_latency'] }} ms</div>
    </div>
</div>

<!-- Filter bar -->
<form method="GET" class="mb-8 bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
    <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-3">Filters</div>
    <div class="flex flex-col md:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search endpoint…"
               class="flex-1 bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
        <select name="status"
                class="bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            <option value="">All Status</option>
            <option value="2xx" @selected(request('status') === '2xx')>2xx</option>
            <option value="4xx" @selected(request('status') === '4xx')>4xx</option>
            <option value="5xx" @selected(request('status') === '5xx')>5xx</option>
        </select>
        <select name="method"
                class="bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            <option value="">All Methods</option>
            <option value="GET" @selected(request('method') === 'GET')>GET</option>
            <option value="POST" @selected(request('method') === 'POST')>POST</option>
            <option value="PUT" @selected(request('method') === 'PUT')>PUT</option>
            <option value="DELETE" @selected(request('method') === 'DELETE')>DELETE</option>
        </select>
        <button type="submit"
                class="px-4 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
            Filter
        </button>
    </div>
</form>

<!-- Logs table -->
<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Time</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Key</th>
                    <th class="py-3 px-4">Method</th>
                    <th class="py-3 px-4">Endpoint</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Latency</th>
                    <th class="py-3 px-4">IP</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6 font-mono text-[10px] text-slate-500">{{ $log->created_at?->format('Y-m-d H:i:s') }}</td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $log->company?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $log->apiKey?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full font-mono text-[9px] font-black uppercase
                                @switch($log->method)
                                    @case('GET') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 @break
                                    @case('POST') bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 @break
                                    @case('PUT') bg-amber-500/20 text-amber-300 border border-amber-500/30 @break
                                    @case('DELETE') bg-red-500/20 text-red-300 border border-red-500/30 @break
                                    @default bg-slate-500/20 text-slate-300 border border-slate-500/30
                                @endswitch">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-300">{{ $log->endpoint }}</td>
                        <td class="py-3.5 px-4 font-mono font-black
                            {{ $log->status_code < 300 ? 'text-emerald-400' : ($log->status_code < 500 ? 'text-amber-400' : 'text-red-400') }}">
                            {{ $log->status_code }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $log->latency_ms }} ms</td>
                        <td class="py-3.5 px-4 font-mono text-[10px] text-slate-500">{{ $log->ip }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-500 text-sm">No requests logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-[#253347]">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
