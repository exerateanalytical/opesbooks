@php
    $__logo = ($company->logo_path ?? null)
        ? public_path('storage/' . ltrim(str_replace('storage/', '', $company->logo_path), '/'))
        : null;
@endphp
<table style="width:100%;border-bottom:3px solid #010048;padding-bottom:12px;margin-bottom:16px">
    <tr>
        <td style="vertical-align:top">
            @if($__logo && file_exists($__logo))
                <img src="{{ $__logo }}" style="max-height:52px;max-width:170px;margin-bottom:3px">
                <div style="font-size:8pt;font-weight:900;color:#010048">{{ strtoupper($company->name ?? 'OPESBOOKS') }}</div>
            @else
                <div style="font-size:18pt;font-weight:900;color:#010048">{{ strtoupper($company->name ?? 'OPESBOOKS') }}</div>
            @endif
            <div style="font-size:7.5pt;color:#64748b;margin-top:3px;line-height:1.5">
                @if($company->niu ?? false)NIU : {{ $company->niu }} @endif@if($company->rccm ?? false) · RCCM : {{ $company->rccm }}@endif
                @if($company->address ?? false)<br>{{ $company->address }}@endif
                @if($company->tax_center ?? false)<br>Centre des Impôts : {{ $company->tax_center }}@endif
            </div>
        </td>
        <td style="vertical-align:top;text-align:right">
            <div style="font-size:15pt;font-weight:900;color:#010048;text-transform:uppercase;letter-spacing:0.5px">{{ $title }}</div>
            @if(!empty($subtitle))<div style="font-size:9.5pt;font-weight:700;color:#C99B0E;margin-top:3px">{{ $subtitle }}</div>@endif
            @if(!empty($docRef))<div style="font-size:8pt;color:#475569;margin-top:2px;font-family:DejaVu Sans Mono,monospace">{{ $docRef }}</div>@endif
        </td>
    </tr>
</table>
