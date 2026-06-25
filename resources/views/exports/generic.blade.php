<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 10px; }
        .brand { font-size: 16px; font-weight: 900; }
        .brand span { color: #C99B0E; }
        .muted { color: #6b7280; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #010048; color: #fff; text-align: left; padding: 6px 8px; font-size: 9px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #f9fafb; }
    </style>
</head>
<body>
    <div class="brand">OPES<span>BOOKS</span></div>
    <div class="muted">{{ $company->name }} · Export {{ strtoupper($type) }} · {{ now()->format('d/m/Y H:i') }}</div>
    <table>
        <thead><tr>@foreach($headings as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
            @forelse($rows as $row)
                <tr>@foreach($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
            @empty
                <tr><td colspan="{{ count($headings) }}" style="text-align:center;padding:20px;color:#9ca3af">Aucune donnée.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
