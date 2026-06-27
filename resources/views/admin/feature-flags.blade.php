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
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Fonctionnalité</th>
                    <th class="py-3 px-4">Clé</th>
                    <th class="py-3 px-4">Activée pour</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($flags as $flag)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6">
                            <div class="font-bold text-white">{{ $flag->name }}</div>
                            @if($flag->description)<div class="text-slate-500 text-[10px] mt-0.5">{{ $flag->description }}</div>@endif
                        </td>
                        <td class="py-3.5 px-4"><span class="font-mono text-[10px] text-slate-400">{{ $flag->key }}</span></td>
                        <td class="py-3.5 px-4">
                            <form method="POST" action="{{ route('admin.feature-flags.update', $flag) }}" class="flex items-center gap-2">
                                @csrf
                                <select name="enabled_for" onchange="this.form.submit()"
                                        class="bg-[#1C2A3A] border border-[#334155] rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-[#F59E0B]/60">
                                    @foreach($targets as $val => $label)
                                        <option value="{{ $val }}" {{ $flag->enabled_for === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $flag->enabled_for === 'none' ? 'bg-slate-500/20 text-slate-400' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' }}">
                                {{ $flag->enabled_for === 'none' ? 'Désactivée' : 'Active' }}
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
