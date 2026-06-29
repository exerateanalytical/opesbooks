@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Platform Toggles</h1>
    <p class="text-slate-500 text-xs mt-1">Global on/off switches for the whole platform. For per-plan / per-company rollout, use <a href="{{ route('admin.feature-flags') }}" class="text-amber-400 hover:text-amber-300">Feature Flags</a>.</p>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl">
    @csrf
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl divide-y divide-slate-800/60"
         x-data>
        @foreach($flags as $key => $flag)
        <label class="flex items-center justify-between gap-6 px-6 py-5 {{ ($flag['wired'] ?? true) ? 'cursor-pointer hover:bg-[#1C2A3A]/30' : 'opacity-60' }} transition-colors">
            <div>
                <div class="text-sm font-black text-white">
                    {{ $flag['label'] }}
                    @unless($flag['wired'] ?? true)
                        <span class="ml-2 text-[9px] font-black uppercase tracking-widest text-slate-500 bg-slate-500/15 border border-slate-500/30 rounded px-1.5 py-0.5">Coming soon</span>
                    @endunless
                </div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $flag['description'] }}</div>
            </div>
            <div x-data="{ on: {{ $flag['enabled'] ? 'true' : 'false' }} }" class="shrink-0">
                <input type="checkbox" name="flags[{{ $key }}]" value="1" x-model="on" class="hidden" @disabled(! ($flag['wired'] ?? true))>
                <button type="button" @click="on = !on" @disabled(! ($flag['wired'] ?? true))
                        class="relative w-12 h-7 rounded-full transition-colors duration-200 {{ ($flag['wired'] ?? true) ? '' : 'cursor-not-allowed' }}"
                        :class="on ? 'bg-amber-500' : 'bg-slate-700'">
                    <span class="absolute top-1 left-1 w-5 h-5 rounded-full bg-white transition-transform duration-200"
                          :class="on ? 'translate-x-5' : ''"></span>
                </button>
            </div>
        </label>
        @endforeach
    </div>

    <div class="mt-6">
        <button type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
            Save Changes
        </button>
    </div>
</form>
@endsection
