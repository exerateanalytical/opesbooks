@extends('admin.layout')

@section('title', $company->name . ' — Data')

@php
    $tabs = [
        'customers'         => 'Clients',
        'suppliers'         => 'Fournisseurs',
        'projects'          => 'Projets',
        'transactions'      => 'Transactions',
        'customer_invoices' => 'Factures clients',
        'supplier_invoices' => 'Factures fourn.',
        'employees'         => 'Employés',
    ];
    $fmt = fn($v) => number_format((float) $v, 0, ',', ' ');
    $thc = 'py-3 px-4 text-left';
    $tdc = 'py-3 px-4';
    $cols = [
        'customers' => 6, 'suppliers' => 5, 'projects' => 6, 'transactions' => 5,
        'customer_invoices' => 5, 'supplier_invoices' => 5, 'employees' => 5,
    ][$tab] ?? 6;
@endphp

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.company', $company) }}" class="text-[10px] text-slate-500 hover:text-slate-300 uppercase tracking-widest font-bold transition-colors">
        ← {{ $company->name }}
    </a>
    <a href="{{ route('admin.company.export', $company) }}" class="text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-white border border-[#253347] rounded-lg px-3 py-1.5">⬇ Export JSON</a>
</div>

<div class="mb-6">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Tenant Data</h1>
    <p class="text-slate-500 text-xs mt-1">Read-only view of {{ $company->name }}'s business records</p>
</div>

<!-- Tabs -->
<div class="flex flex-wrap gap-2 mb-5">
    @foreach($tabs as $key => $label)
    <a href="{{ route('admin.company.data', [$company, 'tab' => $key]) }}"
       class="px-3 py-1.5 rounded-lg text-[11px] font-black uppercase tracking-wide border transition-all
              {{ $tab === $key ? 'bg-amber-500/15 text-amber-300 border-amber-400' : 'text-slate-400 hover:text-white bg-[#151F2E] border-[#253347]' }}">
        {{ $label }}
        <span class="ml-1 opacity-70">{{ number_format($counts[$key]) }}</span>
    </a>
    @endforeach
</div>

<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-[#0B1120]/50">
                    @switch($tab)
                        @case('customers')
                            <th class="{{ $thc }}">Name</th><th class="{{ $thc }}">NIU</th><th class="{{ $thc }}">Email</th><th class="{{ $thc }}">Phone</th><th class="{{ $thc }}">Credit limit</th><th class="{{ $thc }}">Status</th>
                            @break
                        @case('suppliers')
                            <th class="{{ $thc }}">Name</th><th class="{{ $thc }}">NIU</th><th class="{{ $thc }}">Email</th><th class="{{ $thc }}">Phone</th><th class="{{ $thc }}">Status</th>
                            @break
                        @case('projects')
                            <th class="{{ $thc }}">Name</th><th class="{{ $thc }}">Code</th><th class="{{ $thc }}">Client</th><th class="{{ $thc }}">Status</th><th class="{{ $thc }}">Contract</th><th class="{{ $thc }}">Dates</th>
                            @break
                        @case('transactions')
                            <th class="{{ $thc }}">Date</th><th class="{{ $thc }}">Type</th><th class="{{ $thc }}">Memo</th><th class="{{ $thc }}">Status</th><th class="{{ $thc }}">DGI sync</th>
                            @break
                        @case('customer_invoices')
                            <th class="{{ $thc }}">N°</th><th class="{{ $thc }}">Date</th><th class="{{ $thc }}">Client</th><th class="{{ $thc }}">TTC</th><th class="{{ $thc }}">Status</th>
                            @break
                        @case('supplier_invoices')
                            <th class="{{ $thc }}">N°</th><th class="{{ $thc }}">Date</th><th class="{{ $thc }}">Supplier</th><th class="{{ $thc }}">TTC</th><th class="{{ $thc }}">Status</th>
                            @break
                        @case('employees')
                            <th class="{{ $thc }}">Name</th><th class="{{ $thc }}">Position</th><th class="{{ $thc }}">CNPS</th><th class="{{ $thc }}">Gross salary</th><th class="{{ $thc }}">Status</th>
                            @break
                    @endswitch
                </tr>
            </thead>
            <tbody class="font-medium divide-y divide-slate-800/60 text-slate-300">
                @forelse($records as $r)
                    <tr class="hover:bg-[#1C2A3A]/40 transition-colors">
                        @switch($tab)
                            @case('customers')
                                <td class="{{ $tdc }} font-bold text-white">{{ $r->name }}</td>
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ $r->niu ?: '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->email ?: '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->phone ?: '—' }}</td>
                                <td class="{{ $tdc }} font-mono">{{ $fmt($r->credit_limit_xaf) }}</td>
                                <td class="{{ $tdc }}">@include('admin.partials.bool', ['on' => $r->is_active])</td>
                                @break
                            @case('suppliers')
                                <td class="{{ $tdc }} font-bold text-white">{{ $r->name }}</td>
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ $r->niu ?: '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->email ?: '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->phone ?: '—' }}</td>
                                <td class="{{ $tdc }}">@include('admin.partials.bool', ['on' => $r->is_active])</td>
                                @break
                            @case('projects')
                                <td class="{{ $tdc }} font-bold text-white">{{ $r->name }}</td>
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ $r->code ?: '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->client?->name ?? '—' }}</td>
                                <td class="{{ $tdc }}"><span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-500/20 text-slate-300 border border-slate-500/30">{{ $r->status }}</span></td>
                                <td class="{{ $tdc }} font-mono">{{ $fmt($r->contract_value) }}</td>
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ $r->start_date?->format('Y-m-d') ?? '—' }} → {{ $r->end_date?->format('Y-m-d') ?? '—' }}</td>
                                @break
                            @case('transactions')
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ \Illuminate\Support\Str::of((string)$r->posting_date)->before(' ') }}</td>
                                <td class="{{ $tdc }}">{{ $r->posting_type ?: '—' }}</td>
                                <td class="{{ $tdc }} text-slate-400">{{ \Illuminate\Support\Str::limit($r->memo, 50) ?: '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->transaction_status ?: '—' }}</td>
                                <td class="{{ $tdc }}">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border
                                        {{ $r->dgi_sync_status === 'APPROVED' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-slate-500/20 text-slate-300 border-slate-500/30' }}">
                                        {{ $r->dgi_sync_status ?: 'N/A' }}
                                    </span>
                                </td>
                                @break
                            @case('customer_invoices')
                                <td class="{{ $tdc }} font-mono text-[10px] text-white">{{ $r->invoice_number }}</td>
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ $r->invoice_date?->format('Y-m-d') ?? '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->customer?->name ?? '—' }}</td>
                                <td class="{{ $tdc }} font-mono text-amber-400">{{ $fmt($r->amount_ttc) }}</td>
                                <td class="{{ $tdc }}"><span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-500/20 text-slate-300 border border-slate-500/30">{{ $r->status }}</span></td>
                                @break
                            @case('supplier_invoices')
                                <td class="{{ $tdc }} font-mono text-[10px] text-white">{{ $r->invoice_number }}</td>
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ $r->invoice_date?->format('Y-m-d') ?? '—' }}</td>
                                <td class="{{ $tdc }}">{{ $r->supplier?->name ?? '—' }}</td>
                                <td class="{{ $tdc }} font-mono text-amber-400">{{ $fmt($r->amount_ttc) }}</td>
                                <td class="{{ $tdc }}"><span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-500/20 text-slate-300 border border-slate-500/30">{{ $r->status }}</span></td>
                                @break
                            @case('employees')
                                <td class="{{ $tdc }} font-bold text-white">{{ $r->name }}</td>
                                <td class="{{ $tdc }}">{{ $r->position ?: '—' }}</td>
                                <td class="{{ $tdc }} font-mono text-[10px]">{{ $r->cnps_number ?: '—' }}</td>
                                <td class="{{ $tdc }} font-mono">{{ $fmt($r->gross_salary_xaf) }}</td>
                                <td class="{{ $tdc }}">@include('admin.partials.bool', ['on' => $r->is_active])</td>
                                @break
                        @endswitch
                    </tr>
                @empty
                    <tr><td colspan="{{ $cols }}" class="py-12 text-center text-slate-500 text-sm">Aucune donnée pour « {{ $tabs[$tab] }} ».</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())
        <div class="px-6 py-4 border-t border-[#253347]">{{ $records->links() }}</div>
    @endif
</div>
@endsection
