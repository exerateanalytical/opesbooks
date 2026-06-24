{{-- Connectivity State Badge — glass pill --}}

<div class="flex-shrink-0" x-data>

    {{-- ONLINE --}}
    <div x-show="$root.status === 'ONLINE'" x-cloak
         class="glass-pill flex items-center gap-2 px-3 py-1.5"
         style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.28);box-shadow:0 0 16px rgba(16,185,129,0.15)">
        <span class="relative flex h-2 w-2">
            <span class="heartbeat absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-70"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
        </span>
        <span class="text-[10px] font-black text-emerald-300 uppercase tracking-widest whitespace-nowrap">
            Online <span class="hidden sm:inline opacity-70">• Sync</span>
        </span>
    </div>

    {{-- LOCAL_MODE --}}
    <div x-show="$root.status === 'LOCAL_MODE'" x-cloak
         class="glass-pill flex items-center gap-2 px-3 py-1.5"
         style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.28);box-shadow:0 0 16px rgba(245,158,11,0.12)">
        <span class="h-2 w-2 rounded-full bg-amber-400 flex-shrink-0"></span>
        <span class="text-[10px] font-black text-amber-300 uppercase tracking-widest whitespace-nowrap">
            Local <span class="hidden sm:inline opacity-70">• Hors-ligne</span>
        </span>
    </div>

    {{-- SYNCING --}}
    <div x-show="$root.status === 'SYNCING'" x-cloak
         class="glass-pill flex items-center gap-2 px-3 py-1.5"
         style="background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.28);box-shadow:0 0 16px rgba(99,102,241,0.12)">
        <svg class="spin-pulse h-2.5 w-2.5 text-indigo-300 flex-shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest whitespace-nowrap">
            Sync… <span class="hidden sm:inline opacity-70">• Reconnexion</span>
        </span>
    </div>

</div>
