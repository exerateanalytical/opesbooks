<div class="min-h-screen bg-gray-50 flex flex-col font-sans text-gray-800 antialiased" wire:poll.10s>

    <header class="bg-[#0A192F] text-white px-6 py-4 flex flex-wrap justify-between items-center gap-4 shadow-md">
        <div class="flex items-center space-x-3">
            <span class="text-xl font-black tracking-wider text-white">OPES<span class="text-amber-500">BOOKS</span></span>
            <span class="text-xs bg-slate-800 border border-slate-700 px-2.5 py-0.5 rounded font-bold text-gray-300 uppercase tracking-wide">
                {{ $language === 'FR' ? 'Suivi Télétransmission DGI' : 'Live DGI Teletransmission Monitor' }}
            </span>
        </div>
        <div class="flex items-center space-x-4">
            <button wire:click="toggleLanguage"
                    class="bg-amber-500 hover:bg-amber-400 text-slate-950 px-3 py-1 rounded font-black text-xs uppercase tracking-wide transition-all transform active:scale-95">
                {{ $language === 'FR' ? 'English (EN)' : 'Français (FR)' }}
            </button>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto p-4 md:p-6 space-y-6">

        @if (session()->has('success'))
            <div class="bg-emerald-50 border-2 border-emerald-300 text-emerald-900 font-bold px-4 py-3 rounded-lg text-xs shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-50 border-2 border-red-300 text-red-900 font-bold px-4 py-3 rounded-lg text-xs shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter bar --}}
        <div class="bg-white rounded-xl border-2 border-slate-200 shadow-sm p-4 flex flex-wrap justify-between items-center gap-4">
            <div class="w-full md:w-72">
                <input wire:model.live.debounce.300ms="searchQuery"
                       type="text"
                       placeholder="{{ $language === 'FR' ? 'Rechercher Réf, Token, Mémo...' : 'Search Ref, Token, Memo...' }}"
                       class="w-full text-xs font-medium border-2 border-slate-200 rounded-lg py-2 px-3 focus:outline-none focus:border-[#0A192F] text-slate-950">
            </div>

            <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-lg border-2 border-slate-200">
                @foreach ([
                    'ALL'      => ($language === 'FR' ? 'Tous' : 'All'),
                    'APPROVED' => ($language === 'FR' ? 'Approuvés' : 'Approved'),
                    'PENDING'  => ($language === 'FR' ? 'En Attente' : 'Pending'),
                    'REJECTED' => ($language === 'FR' ? 'Rejetés' : 'Rejected'),
                ] as $val => $label)
                    @php
                        $active = $statusFilter === $val;
                        $colors = match($val) {
                            'APPROVED' => 'bg-emerald-600 text-white',
                            'PENDING'  => 'bg-amber-500 text-slate-950',
                            'REJECTED' => 'bg-red-600 text-white',
                            default    => 'bg-[#0A192F] text-white',
                        };
                    @endphp
                    <button wire:click="$set('statusFilter', '{{ $val }}')"
                            class="text-[11px] font-black uppercase px-3 py-1.5 rounded-md tracking-wider transition-colors {{ $active ? $colors : 'text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border-2 border-slate-200 shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900 border-b-2 border-slate-200 text-[11px] font-black uppercase text-slate-300 tracking-wider">
                            <th class="py-2.5 px-4 whitespace-nowrap">{{ $language === 'FR' ? 'Référence' : 'Reference' }}</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">{{ $language === 'FR' ? 'Date & Mémo' : 'Date & Memo' }}</th>
                            <th class="py-2.5 px-4 text-center whitespace-nowrap">{{ $language === 'FR' ? 'Statut DGI' : 'DGI Status' }}</th>
                            <th class="py-2.5 px-4 whitespace-nowrap">{{ $language === 'FR' ? 'Token de Validation' : 'Clearance Token' }}</th>
                            <th class="py-2.5 px-4 text-center whitespace-nowrap w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-100 text-xs font-medium text-slate-950">
                        @forelse ($entries as $entry)
                            <tr class="hover:bg-slate-50 transition-colors {{ $entry->dgi_sync_status === 'REJECTED' ? 'bg-red-50/20' : 'bg-white' }}">

                                <td class="py-3 px-4 font-mono font-black text-slate-900 whitespace-nowrap">
                                    <span class="block text-sm">{{ $entry->reference_id }}</span>
                                    @if ($entry->invoice_crypto_hash)
                                        <span class="text-[9px] text-slate-400 block font-normal max-w-[140px] truncate">
                                            SHA256: {{ $entry->invoice_crypto_hash }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-4">
                                    <span class="block font-bold text-slate-600 font-mono text-[10px]">{{ $entry->posting_date }}</span>
                                    <span class="block text-slate-800 text-[11px] font-medium max-w-xs truncate">{{ $entry->memo }}</span>
                                </td>

                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if ($entry->dgi_sync_status === 'APPROVED')
                                        <span class="inline-block text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full border border-emerald-200">
                                            ✔ {{ $language === 'FR' ? 'APPROUVÉ' : 'APPROVED' }}
                                        </span>
                                    @elseif ($entry->dgi_sync_status === 'PENDING')
                                        <span class="inline-block text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full border border-amber-300 animate-pulse">
                                            ⏳ {{ $language === 'FR' ? 'EN ATTENTE' : 'PENDING' }}
                                        </span>
                                    @else
                                        <span class="inline-block text-[10px] font-black uppercase tracking-wider bg-red-50 text-red-700 px-2.5 py-1 rounded-full border border-red-300">
                                            ✖ {{ $language === 'FR' ? 'REJETÉ' : 'REJECTED' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 font-mono text-slate-700">
                                    @if ($entry->dgi_sync_status === 'APPROVED')
                                        <span class="text-slate-900 font-bold text-[11px] block select-all bg-slate-50 px-2 py-1 rounded border border-slate-200 max-w-[220px] truncate">
                                            {{ $entry->dgi_validation_token }}
                                        </span>
                                        <span class="text-[9px] text-slate-400 block mt-0.5">
                                            {{ $language === 'FR' ? 'Validé le' : 'Cleared at' }}: {{ $entry->dgi_validated_at }}
                                        </span>
                                    @elseif ($entry->dgi_sync_status === 'REJECTED')
                                        <span class="text-red-600 font-bold text-[10px] block bg-red-50/50 p-1.5 rounded border border-red-200 max-w-xs whitespace-normal line-clamp-2">
                                            {{ json_decode($entry->dgi_error_payload)?->message ?? $entry->dgi_error_payload }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">
                                            {{ $language === 'FR' ? 'En attente d\'attribution...' : 'Awaiting clearance...' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if ($entry->dgi_sync_status !== 'APPROVED')
                                        <button wire:click="retrySync({{ $entry->id }})"
                                                wire:loading.attr="disabled"
                                                class="bg-[#0A192F] text-white font-black px-3 py-1.5 rounded shadow text-[10px] uppercase tracking-wider transition-all transform hover:bg-slate-800 active:scale-95 disabled:opacity-50">
                                            {{ $language === 'FR' ? 'Renvoyer' : 'Retry Sync' }}
                                        </button>
                                    @else
                                        <span class="text-[10px] font-black text-emerald-600 uppercase">
                                            {{ $language === 'FR' ? 'Conforme' : 'Compliant' }} ✔
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    {{ $language === 'FR' ? 'Aucune écriture trouvée.' : 'No entries found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($entries->hasPages())
                <div class="bg-slate-50 border-t-2 border-slate-200 px-4 py-3">
                    {{ $entries->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
