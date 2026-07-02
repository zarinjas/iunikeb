<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <title>Penyata Caruman {{ $year }}</title>
    <style>
        body { color: #1e293b; font-family: Arial, sans-serif; font-size: 13px; line-height: 1.6; margin: 36px; }

        .header { border-bottom: 2px solid #0d9488; padding-bottom: 16px; margin-bottom: 24px; }
        .header-top { display: flex; align-items: center; justify-content: space-between; }
        .header-left h1 { font-size: 20px; margin: 0 0 4px; color: #0f172a; }
        .header-left .coop-name { font-size: 14px; color: #475569; margin: 0; }
        .header-right { text-align: right; }
        .header-right .label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 2px; }
        .header-right .value { font-size: 14px; font-weight: 600; color: #0f172a; margin: 0; }

        .title-section { text-align: center; margin-bottom: 28px; }
        .title-section h2 { font-size: 18px; margin: 0 0 4px; color: #0f172a; text-transform: uppercase; }
        .title-section .year-badge { display: inline-block; background: #ccfbf1; color: #0f766e; font-size: 22px; font-weight: 700; padding: 4px 20px; border-radius: 6px; margin-top: 8px; }

        .member-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 28px; }
        .member-info table { width: 100%; }
        .member-info td { padding: 4px 0; }
        .member-info td:first-child { color: #64748b; width: 140px; font-size: 12px; }
        .member-info td:last-child { font-weight: 600; color: #0f172a; font-size: 13px; }

        .amounts { margin-bottom: 24px; }
        .amount-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; margin-bottom: 12px; }
        .amount-card .card-label { font-size: 11px; color: #64748b; margin: 0 0 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .amount-card .card-value { font-size: 24px; font-weight: 700; color: #0f172a; margin: 0; }
        .amount-card .card-desc { font-size: 11px; color: #94a3b8; margin: 4px 0 0; }
        .amount-card.dividen { background: #f0fdf4; border-color: #bbf7d0; }
        .amount-card.dividen .card-value { color: #166534; }

        .summary-card { background: #f0fdfa; border: 1.5px solid #99f6e4; border-radius: 10px; padding: 16px 20px; }
        .summary-card .summary-label { font-size: 12px; color: #0f766e; font-weight: 600; margin: 0 0 4px; }
        .summary-card .summary-value { font-size: 22px; font-weight: 700; color: #0f766e; margin: 0; }

        .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 10px; color: #94a3b8; }
        .footer p { margin: 2px 0; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="header-left">
                <h1>{{ $cooperative['name'] ?? 'Koperasi' }}</h1>
                @if (!empty($cooperative['registration_no']))
                <p class="coop-name">No. Pendaftaran: {{ $cooperative['registration_no'] }}</p>
                @endif
            </div>
            <div class="header-right">
                <p class="label">Tarikh Jana</p>
                <p class="value">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <div class="title-section">
        <h2>Penyata Caruman</h2>
        <div class="year-badge">{{ $year }}</div>
    </div>

    <div class="member-info">
        <table>
            <tr>
                <td>Nama Ahli</td>
                <td>{{ $member['full_name'] }}</td>
            </tr>
            <tr>
                <td>No. Ahli</td>
                <td>{{ $member['member_no'] }}</td>
            </tr>
        </table>
    </div>

    <div class="amounts">
        <div class="amount-card">
            <p class="card-label">Caruman Setakat Ini</p>
            <p class="card-value">RM {{ number_format((float) $contribution['caruman_semasa'], 2) }}</p>
            <p class="card-desc">Jumlah caruman terkumpul bagi tahun {{ $year }}</p>
        </div>

        <div class="amount-card">
            <p class="card-label">Caruman Keseluruhan</p>
            <p class="card-value">RM {{ number_format((float) $contribution['caruman_keseluruhan'], 2) }}</p>
            <p class="card-desc">Jumlah caruman sepanjang masa sehingga tahun {{ $year }}</p>
        </div>

        <div class="amount-card dividen">
            <p class="card-label">Dividen {{ $year }}</p>
            <p class="card-value">RM {{ number_format((float) $contribution['dividen'], 2) }}</p>
            <p class="card-desc">Dividen yang diisytiharkan untuk tahun {{ $year }}</p>
        </div>

        <div class="summary-card">
            <p class="summary-label">Jumlah Caruman + Dividen {{ $year }}</p>
            <p class="summary-value">RM {{ number_format((float) $contribution['caruman_semasa'] + (float) $contribution['dividen'], 2) }}</p>
        </div>
    </div>

    <div class="footer">
        <p>Dijana oleh sistem {{ $cooperative['name'] ?? config('app.name') }} pada {{ now()->format('d/m/Y H:i') }}</p>
        <p>Dokumen ini dijana secara automatik dan tidak memerlukan tandatangan.</p>
    </div>
</body>
</html>
