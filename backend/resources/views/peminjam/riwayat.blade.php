<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Panel Peminjam</a>

        <div class="d-flex">
            <a href="{{ route('peminjam.katalog') }}"
               class="btn btn-outline-light btn-sm me-2">
                Katalog Alat
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-light btn-sm text-primary">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <h3 class="mb-3">Riwayat Peminjaman Saya</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Alat yang Dipinjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($peminjamans as $index => $peminjaman)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <ul class="mb-0">
                                        @foreach($peminjaman->detailPinjams as $detail)
                                            <li>
                                                {{ $detail->alat->nama_alat ?? '-' }}
                                                ({{ $detail->jumlah }} unit)
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>

                                <td>
                                    {{ $peminjaman->tgl_pinjam }}
                                </td>

                                <td>
                                    {{ $peminjaman->tgl_kembali_plan }}
                                </td>

                                <td>
                                    @if($peminjaman->status === 'diajukan')
                                        <span class="badge bg-warning text-dark">Diajukan</span>
                                    @elseif($peminjaman->status === 'dipinjam')
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @elseif($peminjaman->status === 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Telat</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Belum ada riwayat peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>