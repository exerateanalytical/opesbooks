<div class="min-h-screen" wire:poll.10s>

    {{-- Page Header --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white uppercase tracking-wide leading-none">
                {{ $language === 'FR' ? 'Suivi Télétransmission DGI' : 'DGI Live-Link Monitor' }}
            </h1>
            <p class="text-xs text-slate-400 font-medium mt-1.5">
                {{ $language === 'FR' ? 'Statut en temps réel — actualisation auto toutes les 10 secondes' : 'Real-time status — auto-refresh every 10 seconds' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="toggleLanguage"
                    class="glass-btn text-slate-950 font-black px-4 py-1.5 rounded-xl text-[11px] uppercase tracking-widest transition-all active:scale-95">
                {{ $language === 'FR' ? 'English (EN)' : 'Français (FR)' }}
            </button>
            <div class="glass-pill flex items-center gap-2 px-3 py-1.5"
                 style="background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.28)">
                <span class="relative flex h-2 w-2">
                    <span class="heartbeat absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-70"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-400"></span>
                </span>
                <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest">Live</span>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session()->has('success'))
        <div class="mb-5 px-4 py-3 rounded-2xl text-sm font-bold float-in"
             style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.28);color:rgba(110,231,183,1)">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-5 px-4 py-3 rounded-2xl text-sm font-bold float-in"
             style="background:rgba(244,63,94,0.12);border:1px solid rgba(244,63,94,0.28);color:rgba(252,165,165,1)">
            ✖ {{ session('error') }}
        </div>
    @endif

    {{-- Filter bar --}}
    <div class="glass-shimmer rounded-2xl p-4 mb-5 flex flex-wrap justify-between items-center gap-4"
         style="border:1px solid #253347;box-shadow:0 4px 24px rgba(0,0,0,0.4)">

        <div class="w-full md:w-72">
            <input wire:model.live.debounce.300ms="searchQuery"
                   type="text"
                   placeholder="{{ $language === 'FR' ? 'Rechercher Réf, Token, Mémo…' : 'Search Ref, Token, Memo…' }}"
                   class="glass-input w-full rounded-xl py-2.5 px-4 text-xs font-medium">
        </div>

        <div class="flex items-center gap-1.5 p-1 rounded-xl" style="background:rgba(0,0,0,0.2);border:1px solid rgba(37,51,71,0.8)">
            @foreach ([
                'ALL'      => ($language === 'FR' ? 'Tous' : 'All'),
                'APPROVED' => ($language === 'FR' ? 'Approuvés' : 'Approved'),
                'PENDING'  => ($language === 'FR' ? 'En Attente' : 'Pending'),
                'REJECTED' => ($language === 'FR' ? 'Rejetés' : 'Rejected'),
            ] as $val => $label)
                @php
                    $active = $statusFilter === $val;
                    $activeStyle = match($val) {
                        'APPROVED' => 'background:rgba(16,185,129,0.2);border:1px solid rgba(16,185,129,0.4);color:rgb(110,231,183)',
                        'PENDING'  => 'background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.4);color:rgb(252,211,77)',
                        'REJECTED' => 'background:rgba(244,63,94,0.2);border:1px solid rgba(244,63,94,0.4);color:rgb(252,165,165)',
                        default    => 'background:#151F2E;border:1px solid #253347;color:white',
                    };
                @endphp
                <button wire:click="$set('statusFilter', '{{ $val }}')"
                        class="text-[10px] font-black uppercase px-3 py-1.5 rounded-lg tracking-wider transition-all"
                        style="{{ $active ? $activeStyle : 'color:rgba(148,163,184,0.8);border:1px solid transparent' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="glass-shimmer rounded-2xl overflow-hidden"
         style="border:1px solid #253347;box-shadow:0 8px 40px rgba(0,0,0,0.5)">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[10px] font-black uppercase text-slate-500 tracking-widest border-b"
                        style="border-color:rgba(37,51,71,0.8);background:rgba(0,0,0,0.18)">
                        <th class="py-3 px-5 whitespace-nowrap">{{ $language === 'FR' ? 'Référence' : 'Reference' }}</th>
                        <th class="py-3 px-5 whitespace-nowrap">{{ $language === 'FR' ? 'Date & Mémo' : 'Date & Memo' }}</th>
                        <th class="py-3 px-5 text-center whitespace-nowrap">{{ $language === 'FR' ? 'Statut DGI' : 'DGI Status' }}</th>
                        <th class="py-3 px-5 whitespace-nowrap">{{ $language === 'FR' ? 'Token de Validation' : 'Clearance Token' }}</th>
                        <th class="py-3 px-5 text-center whitespace-nowrap w-28">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-medium">
                    @forelse ($entries as $entry)
                        <tr class="border-b transition-colors"
                            style="border-color:rgba(28,42,58,0.6);{{ $entry->dgi_sync_status === 'REJECTED' ? 'background:rgba(244,63,94,0.04)' : '' }}"
                            onmouseenter="this.style.background='rgba(28,42,58,0.4)'"
                            onmouseleave="this.style.background='{{ $entry->dgi_sync_status === 'REJECTED' ? 'rgba(244,63,94,0.04)' : 'transparent' }}'">

                            <td class="py-3.5 px-5 font-mono whitespace-nowrap">
                                <span class="block text-sm font-black text-white">{{ $entry->reference_id }}</span>
                                @if ($entry->invoice_crypto_hash)
                                    <span class="text-[9px] text-slate-500 block font-normal max-w-[140px] truncate mt-0.5">
                                        SHA256: {{ $entry->invoice_crypto_hash }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5">
                                <span class="block font-mono text-[10px] text-slate-400 font-bold">{{ $entry->posting_date }}</span>
                                <span class="block text-slate-300 text-[11px] font-medium max-w-xs truncate mt-0.5">{{ $entry->memo }}</span>
                            </td>

                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                @if ($entry->dgi_sync_status === 'APPROVED')
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full"
                                          style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);color:rgb(110,231,183)">
                                        ✔ {{ $language === 'FR' ? 'APPROUVÉ' : 'APPROVED' }}
                                    </span>
                                @elseif ($entry->dgi_sync_status === 'PENDING')
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full animate-pulse"
                                          style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.35);color:rgb(252,211,77)">
                                        ⏳ {{ $language === 'FR' ? 'EN ATTENTE' : 'PENDING' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full"
                                          style="background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.35);color:rgb(252,165,165)">
                                        ✖ {{ $language === 'FR' ? 'REJETÉ' : 'REJECTED' }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 font-mono">
                                @if ($entry->dgi_sync_status === 'APPROVED')
                                    <span class="text-amber-400 font-bold text-[11px] block select-all px-2 py-1 rounded-lg max-w-[220px] truncate"
                                          style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2)">
                                        {{ $entry->dgi_validation_token }}
                                    </span>
                                    <span class="text-[9px] text-slate-500 block mt-1">
                                        {{ $language === 'FR' ? 'Validé le' : 'Cleared at' }}: {{ $entry->dgi_validated_at }}
                                    </span>
                                @elseif ($entry->dgi_sync_status === 'REJECTED')
                                    <span class="text-rose-400 font-bold text-[10px] block p-1.5 rounded-lg max-w-xs whitespace-normal line-clamp-2"
                                          style="background:rgba(244,63,94,0.08);border:1px solid rgba(244,63,94,0.2)">
                                        {{ json_decode($entry->dgi_error_payload)?->message ?? $entry->dgi_error_payload }}
                                    </span>
                                @else
                                    <span class="text-slate-500 italic text-[11px]">
                                        {{ $language === 'FR' ? 'En attente d\'attribution…' : 'Awaiting clearance…' }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                @if ($entry->dgi_sync_status !== 'APPROVED')
                                    <button wire:click="retrySync({{ $entry->id }})"
                                            wire:loading.attr="disabled"
                                            class="glass-btn-dark text-slate-200 font-black px-3 py-1.5 rounded-xl text-[10px] uppercase tracking-wider transition-all active:scale-95 disabled:opacity-40">
                                        {{ $language === 'FR' ? 'Renvoyer' : 'Retry Sync' }}
                                    </button>
                                @else
                                    <span class="text-[10px] font-black uppercase tracking-wider"
                                          style="color:rgb(110,231,183)">
                                        {{ $language === 'FR' ? 'Conforme' : 'Compliant' }} ✔
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl"
                                         style="background:rgba(28,42,58,0.6);border:1px solid rgba(37,51,71,0.8)">
                                        📡
                                    </div>
                                    <div class="text-slate-500 text-[11px] font-bold uppercase tracking-widest">
                                        {{ $language === 'FR' ? 'Aucune écriture trouvée.' : 'No entries found.' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($entries->hasPages())
            <div class="px-5 py-3 border-t" style="border-color:rgba(37,51,71,0.8);background:rgba(0,0,0,0.1)">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

</div>
