@extends('admin.layout')

@section('title', 'API Keys')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">API Keys</h1>
    <p class="text-slate-500 text-xs mt-1">Issue and manage developer keys</p>
</div>

@if($newKey)
    <div x-data="{ copied: false }" class="mb-8 bg-[#151F2E] border border-emerald-500/40 rounded-2xl p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-[9px] font-black uppercase tracking-widest text-emerald-400">✓ API Key Generated</div>
                <p class="text-slate-400 text-xs mt-1">Copy it now — it will not be shown again.</p>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <code class="flex-1 bg-[#0B1120] border border-[#253347] rounded-xl px-4 py-3 font-mono text-xs text-emerald-300 break-all">{{ $newKey }}</code>
            <button type="button"
                    @click="navigator.clipboard.writeText(@js($newKey)); copied = true; setTimeout(() => copied = false, 2000)"
                    class="shrink-0 px-4 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
                <span x-show="!copied">Copy</span>
                <span x-show="copied" x-cloak>Copied!</span>
            </button>
        </div>
    </div>
@endif

<!-- Issue New Key -->
<div x-data="{ open: false }" class="mb-8 bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-[#253347]">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Issue New Key</div>
        <button type="button" @click="open = !open"
                class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-amber-400 bg-amber-500/15 border border-amber-500/30 hover:bg-amber-500/25 transition-all">
            <span x-show="!open">+ Issue New Key</span>
            <span x-show="open" x-cloak>Cancel</span>
        </button>
    </div>
    <form x-show="open" x-cloak method="POST" action="{{ route('admin.api-keys.store') }}" class="p-6 space-y-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1.5">Company</label>
                <select name="company_id" required
                        class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1.5">Key Name</label>
                <input type="text" name="name" required placeholder="e.g. Production integration"
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1.5">Environment</label>
                <select name="environment"
                        class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
                    <option value="live">Live</option>
                    <option value="test">Test</option>
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1.5">Rate Limit (per hour)</label>
                <input type="number" name="rate_limit" value="1000" min="1"
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1.5">Expires At <span class="text-slate-600 normal-case font-medium">(optional)</span></label>
                <input type="date" name="expires_at"
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
        </div>
        <div>
            <label class="block text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Scopes</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                @foreach($scopes as $scope)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-xl bg-[#1C2A3A] border border-[#334155] cursor-pointer hover:border-amber-500/40 transition-all">
                        <input type="checkbox" name="scopes[]" value="{{ $scope }}"
                               class="w-3.5 h-3.5 rounded bg-[#151F2E] border-slate-600 text-amber-500 focus:ring-0 focus:ring-offset-0">
                        <span class="font-mono text-[11px] text-slate-300">{{ $scope }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="pt-1">
            <button type="submit"
                    class="px-4 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
                Generate Key
            </button>
        </div>
    </form>
</div>

<!-- Keys table -->
<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-[#0B1120]/50">
                    <th class="py-3 px-6">Name</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Key</th>
                    <th class="py-3 px-4">Env</th>
                    <th class="py-3 px-4">Scopes</th>
                    <th class="py-3 px-4">Rate</th>
                    <th class="py-3 px-4">Last Used</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($keys as $key)
                    <tr class="hover:bg-[#1C2A3A]/40 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-white">{{ $key->name }}</td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $key->company?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4 font-mono text-[10px] text-slate-400">{{ $key->maskedKey() }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $key->environment === 'live'
                                    ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30'
                                    : 'bg-slate-500/20 text-slate-300 border border-slate-500/30' }}">
                                {{ $key->environment }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-500/20 text-slate-300 border border-slate-500/30">
                                {{ count($key->scopes ?? []) }} scopes
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-400 font-mono text-[10px]">{{ $key->rate_limit . '/h' }}</td>
                        <td class="py-3.5 px-4 text-slate-500 text-[10px]">{{ $key->last_used_at?->diffForHumans() ?? 'never' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $key->status === 'ACTIVE'
                                    ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
                                    : 'bg-red-500/20 text-red-300 border border-red-500/30' }}">
                                {{ $key->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            @if($key->status === 'ACTIVE')
                                <form method="POST" action="{{ route('admin.api-keys.revoke', $key) }}"
                                      onsubmit="return confirm('Revoke this API key? This cannot be undone.')">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-red-500/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 transition-all">
                                        Revoke
                                    </button>
                                </form>
                            @else
                                <span class="text-slate-600">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-500 text-sm">No API keys issued yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($keys->hasPages())
        <div class="px-6 py-4 border-t border-[#253347]">
            {{ $keys->links() }}
        </div>
    @endif
</div>
@endsection
