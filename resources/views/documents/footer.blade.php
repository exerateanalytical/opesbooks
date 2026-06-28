@php
    $__stamp = app(\App\Services\DocumentStamp::class)->for($company, $docType ?? 'DOCUMENT', $docRef ?? null);
    $__vurl  = preg_replace('#^https?://#', '', $__stamp['verify_url']);
@endphp
<table style="width:100%;border-top:1px solid #e2e8f0;margin-top:20px;padding-top:10px">
    <tr>
        <td style="vertical-align:top;font-size:7pt;color:#94a3b8;line-height:1.6">
            <strong style="color:#475569">Réf. document :</strong>
            <span style="font-family:DejaVu Sans Mono,monospace">{{ $__stamp['ref'] }}</span><br>
            <strong style="color:#475569">Généré le :</strong> {{ $__stamp['timestamp'] }} · par OPESBooks<br>
            <strong style="color:#475569">Vérifier en ligne :</strong> {{ $__vurl }}
            @if(!empty($extraFooter))<br>{{ $extraFooter }}@endif
        </td>
        <td style="width:92px;text-align:right;vertical-align:top">
            <img src="{{ $__stamp['qr'] }}" style="width:78px;height:78px">
            <div style="font-size:6pt;color:#94a3b8;text-align:center;margin-top:1px">Scanner pour vérifier</div>
        </td>
    </tr>
</table>
