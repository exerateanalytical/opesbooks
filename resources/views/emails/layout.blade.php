<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:24px;background:#010048;font-family:Inter,Arial,sans-serif;color:#e2e8f0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr><td align="center">
            <table role="presentation" width="520" cellpadding="0" cellspacing="0"
                   style="max-width:520px;background:#010057;border:1px solid rgba(255,255,255,0.1);border-radius:12px;overflow:hidden;">
                <tr><td style="padding:24px 28px 16px;border-bottom:2px solid #C99B0E;">
                    <span style="font-size:20px;font-weight:900;letter-spacing:2px;color:#fff;">OPES<span style="color:#C99B0E;">BOOKS</span></span>
                </td></tr>
                <tr><td style="padding:28px;font-size:15px;line-height:1.6;color:#cbd5e1;">
                    @yield('content')
                    @hasSection('action')
                    <div style="margin-top:24px;">@yield('action')</div>
                    @endif
                </td></tr>
                <tr><td style="padding:18px 28px;border-top:1px solid rgba(255,255,255,0.08);font-size:11px;color:#64748b;">
                    © {{ date('Y') }} OPESBooks · Douala, Cameroun · <a href="{{ url('/app') }}" style="color:#C99B0E;text-decoration:none;">opesbooks.cm</a>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
