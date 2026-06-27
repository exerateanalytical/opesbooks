@extends('admin.layout')

@section('title', 'Developer Portal')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Developer Portal</h1>
    <p class="text-slate-500 text-xs mt-1">OpesBooks API — integrate against the platform</p>
</div>

<!-- Authentication -->
<div class="mb-8 bg-[#151F2E] border border-[#253347] rounded-2xl p-6">
    <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-3">Authentication</div>
    <p class="text-slate-400 text-sm leading-relaxed mb-4">
        Every request to the OpesBooks API must include a bearer token in the
        <code class="font-mono text-amber-400 bg-amber-500/15 border border-amber-500/30 rounded px-1.5 py-0.5 text-xs">Authorization</code>
        header. Live keys are prefixed
        <code class="font-mono text-amber-400 bg-amber-500/15 border border-amber-500/30 rounded px-1.5 py-0.5 text-xs">ob_live_sk_…</code>,
        test keys with <code class="font-mono text-slate-300 bg-slate-500/15 border border-slate-500/30 rounded px-1.5 py-0.5 text-xs">ob_test_sk_…</code>.
        Keep your secret keys server-side — never expose them in client code.
    </p>
    <pre class="bg-[#0B1120] border border-[#253347] rounded-xl p-4 font-mono text-xs text-slate-300 overflow-x-auto leading-relaxed"><span class="text-emerald-400">curl</span> -X POST https://opesbooks.cm/api/v1/integration/invoices \
  -H <span class="text-amber-400">"Authorization: Bearer ob_live_sk_xxxxxxxxxxxxxxxx"</span> \
  -H <span class="text-amber-400">"Content-Type: application/json"</span> \
  -d '{
    <span class="text-indigo-300">"client_name"</span>: <span class="text-emerald-300">"Société Exemple SARL"</span>,
    <span class="text-indigo-300">"amount"</span>: <span class="text-emerald-300">250000</span>,
    <span class="text-indigo-300">"tax_rate"</span>: <span class="text-emerald-300">19.25</span>
  }'</pre>
</div>

<!-- Endpoints -->
<div class="mb-8 bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-[#253347]">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Endpoints</div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Method</th>
                    <th class="py-3 px-4">Path</th>
                    <th class="py-3 px-4">Description</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @php
                    $endpoints = [
                        ['GET',  '/api/v1/integration/invoices',         'List invoices'],
                        ['POST', '/api/v1/integration/invoices',         'Create invoice'],
                        ['GET',  '/api/v1/integration/journal',          'List journal entries'],
                        ['GET',  '/api/v1/integration/reports/pl',       'P&L report'],
                        ['GET',  '/api/v1/integration/tax/vat-summary',  'VAT summary'],
                    ];
                    $methodColors = [
                        'GET'    => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
                        'POST'   => 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30',
                        'PUT'    => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                        'DELETE' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                    ];
                @endphp
                @foreach($endpoints as [$method, $path, $desc])
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6">
                            <span class="px-2 py-0.5 rounded-full font-mono text-[9px] font-black uppercase {{ $methodColors[$method] ?? 'bg-slate-500/20 text-slate-300 border border-slate-500/30' }}">
                                {{ $method }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-300">{{ $path }}</td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $desc }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Rate limits & scopes -->
<div class="mb-8 bg-[#151F2E] border border-[#253347] rounded-2xl p-6">
    <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-3">Rate Limits &amp; Scopes</div>
    <p class="text-slate-400 text-sm leading-relaxed mb-2">
        Each API key enforces a configurable <span class="text-amber-400 font-bold">hourly rate limit</span> (default 1000 requests/hour).
        Exceeding the limit returns
        <code class="font-mono text-red-300 bg-red-500/15 border border-red-500/30 rounded px-1.5 py-0.5 text-xs">429 Too Many Requests</code>
        with a <code class="font-mono text-slate-300 text-xs">Retry-After</code> header.
    </p>
    <p class="text-slate-400 text-sm leading-relaxed mb-4">
        Keys are scoped to the minimum permissions required. A request to an endpoint outside a key's granted scopes returns
        <code class="font-mono text-amber-300 bg-amber-500/15 border border-amber-500/30 rounded px-1.5 py-0.5 text-xs">403 Forbidden</code>.
    </p>
    <div class="flex flex-wrap gap-2">
        @foreach(['invoices:read', 'invoices:write', 'journal:read', 'journal:write', 'reports:read', 'tax:read'] as $scope)
            <span class="px-2.5 py-1 rounded-full font-mono text-[10px] font-black uppercase bg-amber-500/15 text-amber-400 border border-amber-500/30">
                {{ $scope }}
            </span>
        @endforeach
    </div>
</div>
@endsection
