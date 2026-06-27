{{--
  Individual MoMo transaction row — SYSCOHADA mapping display
  Invariants: XAF format, HT/TVA/CAC/TTC isolation, bilingual status labels, 7:1 contrast
--}}

@props(['transaction'])

@php
    $isResolved  = $transaction['status'] === 'RESOLVED';
    $isRevenue   = $transaction['type'] === 'REVENUE';
    $rowBg       = $isResolved ? '' : 'bg-amber-500/5';
    $statusColor = $isResolved
        ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/30'
        : 'text-amber-400 bg-amber-500/10 border-amber-500/30';
    $statusLabel = $isResolved
        ? 'Validé Auto / Auto-Matched'
        : 'À Résoudre / Requires Review';

    // Tax breakdown
    $tax        = $transaction['tax'] ?? [];
    $htFmt      = number_format((float)($tax['amount_ht']  ?? 0), 0, '.', ',');
    $vatFmt     = number_format((float)($tax['base_vat']   ?? 0), 0, '.', ',');
    $cacFmt     = number_format((float)($tax['cac']        ?? 0), 0, '.', ',');
    $ttcFmt     = number_format((float)($transaction['amount'] ?? 0), 0, '.', ',');

    // SYSCOHADA mappings
    $debitCode  = $transaction['debit_account']  ?? null;
    $creditCode = $transaction['credit_account'] ?? null;

    $operator   = strtoupper($transaction['operator'] ?? 'MTN');
    $opBg       = $operator === 'ORANGE' ? 'bg-orange-500' : 'bg-yellow-400';
    $opText     = $operator === 'ORANGE' ? 'text-white'    : 'text-slate-950';
@endphp

<tr class="tbl-row transition-colors {{ $rowBg }}">

    {{-- ID Reference + Operator Badge --}}
    <td class="py-3 px-4 font-mono font-black text-slate-200 whitespace-nowrap">
        <span class="block text-slate-100 text-[11px]">{{ $transaction['transaction_id'] }}</span>
        <div class="flex items-center gap-1 mt-0.5">
            <span class="text-[9px] font-black px-1.5 py-0.5 rounded {{ $opBg }} {{ $opText }} uppercase tracking-widest">
                {{ $operator }}
            </span>
            <span class="text-[9px] uppercase font-black tracking-widest px-1.5 py-0.5 rounded border {{ $statusColor }}">
                {{ $isResolved ? 'Auto-Matched' : 'À Résoudre' }}
            </span>
        </div>
    </td>

    {{-- Raw Payload Description --}}
    <td class="py-3 px-4 max-w-xs md:max-w-sm truncate text-slate-300 font-mono text-[11px]"
        title="{{ $transaction['message'] ?? '' }}">
        {{ $transaction['message'] ?? '—' }}
    </td>

    {{-- Gross Amount TTC — XAF format --}}
    <td class="py-3 px-4 text-right font-mono font-black text-slate-100 whitespace-nowrap text-sm">
        {{ $ttcFmt }} <span class="text-[10px] text-slate-500 font-bold">XAF</span>
    </td>

    {{-- Tax Math Matrix: HT | TVA | CAC isolated per spec --}}
    <td class="py-3 px-4 whitespace-nowrap">
        <div class="flex flex-col items-center justify-center text-[10px] font-mono leading-tight
                    {{ $isResolved ? 'border-slate-700 bg-transparent' : 'bg-amber-500/10 border-amber-500/30' }}
                    p-1.5 rounded border">
            <div class="font-bold text-slate-200">HT: {{ $htFmt }} XAF</div>
            <div class="text-slate-500">TVA (17.5%): {{ $vatFmt }} | CAC (10%): {{ $cacFmt }}</div>
            <div class="{{ $isResolved ? 'text-slate-400' : 'text-amber-400' }} font-bold">TTC: {{ $ttcFmt }} XAF</div>
        </div>
    </td>

    {{-- SYSCOHADA Account Mapping --}}
    <td class="py-3 px-4 text-center whitespace-nowrap">
        @if($debitCode && $creditCode)
            <div class="inline-flex flex-col space-y-0.5">
                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded" style="background:#1C2A3A;border:1px solid #253347;color:#93C5FD">
                    D: {{ $debitCode }}
                </span>
                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded" style="background:#1C2A3A;border:1px solid #253347;color:#86EFAC">
                    C: {{ $creditCode }}
                </span>
            </div>
        @else
            <span class="text-[10px] font-bold text-amber-700 bg-amber-100 border border-amber-200 px-2 py-1 rounded block max-w-[160px] mx-auto">
                ⚠ Sélectionner catégorie de dépense / Select Expense Category (Classe 6 manquante)
            </span>
        @endif
    </td>

    {{-- Action Button --}}
    <td class="py-3 px-4 text-center whitespace-nowrap">
        @if($isResolved)
            <button
                class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-3 py-1.5 rounded shadow text-[11px] uppercase tracking-wider transition-transform transform active:scale-95"
                onclick="approveTransaction('{{ $transaction['transaction_id'] }}')"
            >
                Valider / Approve
            </button>
        @else
            <button
                class="bg-amber-500 hover:bg-amber-600 text-slate-950 font-black px-3 py-1.5 rounded shadow text-[11px] uppercase tracking-wider transition-transform transform active:scale-95"
                onclick="resolveTransaction('{{ $transaction['transaction_id'] }}')"
            >
                Résoudre / Map
            </button>
        @endif
    </td>

</tr>
