@props([
    'transactions' => [],
    'unresolvedCount' => 0,
])

<div class="w-full rounded-2xl overflow-hidden relative"
     style="background:rgba(255,255,255,0.055);backdrop-filter:blur(28px) saturate(180%);-webkit-backdrop-filter:blur(28px) saturate(180%);border:1px solid rgba(255,255,255,0.13);box-shadow:0 8px 40px rgba(0,0,0,0.5),0 1px 0 rgba(255,255,255,0.12) inset;">

    {{-- Header --}}
    <div class="px-5 py-4 flex flex-wrap justify-between items-center gap-2 border-b"
         style="border-color:rgba(255,255,255,0.08);background:rgba(255,255,255,0.04)">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-[9px] font-black text-slate-950"
                 style="background:linear-gradient(135deg,rgba(245,158,11,0.95),rgba(160,124,8,0.95));box-shadow:0 4px 14px rgba(245,158,11,0.35)">
                MOMO
            </div>
            <div>
                <h2 class="text-sm font-black text-white uppercase tracking-wide leading-none">Flux de Transactions</h2>
                <p class="text-[10px] text-slate-400 font-medium mt-0.5">Inbound • MTN &amp; Orange APIs</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full"
                  style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.28);color:rgba(252,211,77,1)">
                Non résolus: <span class="font-mono font-black">{{ str_pad($unresolvedCount, 2, '0', STR_PAD_LEFT) }}</span>
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black uppercase text-slate-500 tracking-widest border-b"
                    style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.15)">
                    <th class="py-3 px-5 whitespace-nowrap">Référence / Opérateur</th>
                    <th class="py-3 px-5 whitespace-nowrap">Description Payload</th>
                    <th class="py-3 px-5 whitespace-nowrap text-right">Montant TTC</th>
                    <th class="py-3 px-5 whitespace-nowrap text-center">Fiscal 19.25%</th>
                    <th class="py-3 px-5 whitespace-nowrap text-center">SYSCOHADA</th>
                    <th class="py-3 px-5 text-center whitespace-nowrap w-32">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium text-slate-300">

                @forelse($transactions as $txn)
                    <x-momo-row :transaction="$txn" />
                @empty
                    <tr>
                        <td colspan="6" class="py-14 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                     style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">
                                    📭
                                </div>
                                <div class="text-slate-500 text-[11px] font-bold uppercase tracking-widest">
                                    Aucune transaction dans le pipeline
                                </div>
                                <div class="text-slate-600 text-[10px]">
                                    Les paiements MoMo apparaîtront ici automatiquement
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($pagination))
        <div class="px-5 py-3.5 flex flex-wrap justify-between items-center gap-2 border-t"
             style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.1)">
            <div class="text-[10px] text-slate-500 font-medium">
                Affichage <span class="text-slate-300 font-bold">{{ $pagination['from'] }}–{{ $pagination['to'] }}</span>
                sur <span class="text-slate-300 font-bold">{{ $pagination['total'] }}</span> transactions
            </div>
            <div class="flex items-center gap-1.5">
                @if($pagination['prev_page_url'])
                    <a href="{{ $pagination['prev_page_url'] }}"
                       class="glass-card text-slate-300 hover:text-white font-black px-3 py-1.5 rounded-lg text-[10px] uppercase tracking-wider transition-all">
                        ← Préc.
                    </a>
                @endif
                @if($pagination['next_page_url'])
                    <a href="{{ $pagination['next_page_url'] }}"
                       class="glass-btn text-slate-950 font-black px-3 py-1.5 rounded-lg text-[10px] uppercase tracking-wider transition-all">
                        Suiv. →
                    </a>
                @endif
            </div>
        </div>
    @endif

</div>
