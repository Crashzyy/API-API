<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat</title>
    <style>
        @page { margin: 24px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 8px; }
        h1 { margin: 0; text-align: center; font-size: 18px; }
        .subtitle { margin: 5px 0 14px; text-align: center; color: #6b7280; font-size: 10px; }
        .filter { margin-bottom: 12px; padding: 8px; border: 1px solid #d1d5db; background: #f3f4f6; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { padding: 6px 4px; border: 1px solid #9ca3af; background: #1f2937; color: #fff; font-size: 7px; text-align: center; }
        td { padding: 5px 4px; border: 1px solid #d1d5db; vertical-align: top; word-wrap: break-word; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .empty { padding: 16px; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman Alat</h1>
    <div class="subtitle">Sistem Peminjaman Alat</div>

    @if($mulai || $selesai || $status)
        <div class="filter">
            Periode: {{ $mulai ?: 'awal' }} s/d {{ $selesai ?: 'sekarang' }}
            @if($status) &nbsp;|&nbsp; Status: {{ ucfirst($status) }} @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="empty">Belum ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
