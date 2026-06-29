@extends('admin.layout')

@section('title', 'Feature Flags')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Fonctionnalités</h1>
    <p class="text-slate-500 text-xs mt-1">Activez les modules par plan ou par entreprise</p>
</div>

<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-[#0B1120]/50">
                    <th class="py-3 px-6">Fonctionnalité</th>
                    <th class="py-3 px-4">Clé</th>
                    <th class="py-3 px-4">Activée pour</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($flags as $flag)
                    @php
                        $ids = $flag->specific_company_ids ?? [];
                        // 'specific_companies' with an empty list resolves to nobody — show as inactive.
                        $effectiveActive = $flag->enabled_for !== 'none'
                            && ! ($flag->enabled_for === 'specific_companies' && count($ids) === 0);
                    @endphp
                    <tr class="hover:bg-[#1C2A3A]/40 transition-colors" x-data="{ target: '{{ $flag->enabled_for }}' }">
                        <td class="py-3.5 px-6">
                            <div class="font-bold text-white">{{ $flag->name }}</div>
                            @if($flag->description)<div class="text-slate-500 text-[10px] mt-0.5">{{ $flag->description }}</div>@endif
                        </td>
                        <td class="py-3.5 px-4"><span class="font-mono text-[10px] text-slate-400">{{ $flag->key }}</span></td>
                        <td class="py-3.5 px-4">
                            <form method="POST" action="{{ route('admin.feature-flags.update', $flag) }}" class="flex flex-wrap items-start gap-2">
                                @csrf
                                <select name="enabled_for" x-model="target"
                                        class="bg-[#1C2A3A] border border-[#334155] rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#F59E0B]/60">
                                    @foreach($targets as $val => $label)
                                        <option value="{{ $val }}" {{ $flag->enabled_for === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <select x-show="target === 'specific_companies'" x-cloak name="specific_company_ids[]" multiple size="4"
                                        class="bg-[#1C2A3A] border border-[#334155] rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#F59E0B]/60 min-w-[180px]">
                                    @foreach($companies as $co)
                                        <option value="{{ $co->id }}" {{ in_array($co->id, $ids) ? 'selected' : '' }}>{{ $co->name }}</option>
                                    @endforeach
                                </select>
                                <button class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wide bg-amber-500/15 text-amber-300 border border-amber-500/30 hover:bg-amber-500/25">Save</button>
                            </form>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $effectiveActive ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-500/20 text-slate-400' }}">
                                @if($flag->enabled_for === 'specific_companies')
                                    {{ count($ids) }} {{ \Illuminate\Support\Str::plural('entreprise', count($ids)) }}
                                @else
                                    {{ $effectiveActive ? 'Active' : 'Désactivée' }}
                                @endif
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-12 text-center text-slate-500 text-sm">Aucune fonctionnalité.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
