{{--
  MoMo Feed Transaction Component
  Dual-ingestion pipeline ledger workspace — data-dense tabular view
  Compliant: SYSCOHADA account mapping, XAF currency display, 7:1 contrast, bilingual labels
--}}

@props([
    'transactions' => [],
    'unresolvedCount' => 0,
])

<div class="w-full bg-white rounded-xl border-2 border-slate-200 shadow-md overflow-hidden font-sans">

    {{-- ===== Component Context Header ===== --}}
    <div class="bg-slate-900 px-4 py-3 flex flex-wrap justify-between items-center gap-2 border-b-2 border-slate-700">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-amber-500 rounded text-slate-950 font-black text-xs tracking-wider">MOMO FEED</div>
            <div>
                <h2 class="text-sm font-black text-white uppercase tracking-wide">Flux de Transactions Reçues</h2>
                <p class="text-[10px] text-slate-400 font-medium">Inbound Pipeline • Auto-parsed via MTN &amp; Orange APIs</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-[11px] font-bold text-slate-300 bg-slate-800 px-2.5 py-1 rounded border border-slate-700">
                Éléments non résolus / Unresolved: <span class="text-amber-400 font-mono font-black">{{ str_pad($unresolvedCount, 2, '0', STR_PAD_LEFT) }}</span>
            </span>
        </div>
    </div>

    {{-- ===== Data-Dense Tabular Ledger Workspace ===== --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-100 border-b-2 border-slate-200 text-[11px] font-black uppercase text-slate-700 tracking-wider">
                    <th class="py-2.5 px-4 whitespace-nowrap">ID Référence / Opérateur</th>
                    <th class="py-2.5 px-4 whitespace-nowrap">Description Originale (Payload Brut)</th>
                    <th class="py-2.5 px-4 whitespace-nowrap text-right">Montant Brut (TTC)</th>
                    <th class="py-2.5 px-4 whitespace-nowrap text-center">Matrice Fiscale (19.25%)</th>
                    <th class="py-2.5 px-4 whitespace-nowrap text-center">Mappage SYSCOHADA</th>
                    <th class="py-2.5 px-4 text-center whitespace-nowrap w-36">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y-2 divide-slate-100 text-xs font-medium text-slate-950">

                @forelse($transactions as $txn)
                    <x-momo-row :transaction="$txn" />
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-400 font-bold text-sm">
                            Aucune transaction dans le pipeline. / No transactions in pipeline.
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ===== Footer Pagination Context ===== --}}
    @if(isset($pagination))
        <div class="bg-slate-50 border-t-2 border-slate-200 px-4 py-3 flex flex-wrap justify-between items-center gap-2 text-xs font-bold text-slate-600">
            <div>
                Affichage de <span class="text-slate-950">{{ $pagination['from'] }}–{{ $pagination['to'] }}</span>
                sur <span class="text-slate-950">{{ $pagination['total'] }}</span> transactions synchronisées
            </div>
            <div class="flex items-center space-x-1">
                @if($pagination['prev_page_url'])
                    <a href="{{ $pagination['prev_page_url'] }}" class="bg-white border-2 border-slate-200 hover:border-slate-400 text-slate-700 font-black px-3 py-1 rounded text-[11px] uppercase tracking-wider transition-colors">
                        ← Précédent
                    </a>
                @endif
                @if($pagination['next_page_url'])
                    <a href="{{ $pagination['next_page_url'] }}" class="bg-slate-900 hover:bg-slate-700 text-white font-black px-3 py-1 rounded text-[11px] uppercase tracking-wider transition-colors">
                        Suivant →
                    </a>
                @endif
            </div>
        </div>
    @endif

</div>
