{{-- Connectivity State Badge — permanent viewport header element --}}
{{-- Three invariant states: ONLINE | LOCAL_MODE | SYNCING --}}

<div class="flex-shrink-0" x-data x-bind:class="$root.status">

    {{-- ONLINE --}}
    <div x-show="$root.status === 'ONLINE'" x-cloak
         class="flex items-center space-x-2 bg-emerald-900/60 border border-emerald-500/40 px-3 py-1.5 rounded-full">
        <span class="relative flex h-2.5 w-2.5">
            <span class="heartbeat absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
        </span>
        <span class="text-[11px] font-black text-emerald-300 uppercase tracking-wider whitespace-nowrap">
            Online <span class="hidden sm:inline">• Cloud Sync Connected</span>
        </span>
    </div>

    {{-- LOCAL_MODE --}}
    <div x-show="$root.status === 'LOCAL_MODE'" x-cloak
         class="flex items-center space-x-2 bg-amber-900/60 border border-amber-500/40 px-3 py-1.5 rounded-full">
        <span class="h-2.5 w-2.5 rounded-full bg-amber-400 flex-shrink-0"></span>
        <span class="text-[11px] font-black text-amber-300 uppercase tracking-wider whitespace-nowrap">
            Mode Local <span class="hidden sm:inline">• Stockage Hors-ligne Actif</span>
        </span>
    </div>

    {{-- SYNCING --}}
    <div x-show="$root.status === 'SYNCING'" x-cloak
         class="flex items-center space-x-2 bg-indigo-900/60 border border-indigo-500/40 px-3 py-1.5 rounded-full">
        <svg class="spin-slow h-3 w-3 text-indigo-300 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-[11px] font-black text-indigo-300 uppercase tracking-wider whitespace-nowrap">
            Synchronisation <span class="hidden sm:inline">• Récupération des données...</span>
        </span>
    </div>

</div>
