<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Panel Petugas</a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-light btn-sm text-primary">
                Logout
            </button>
        </form>
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

    <h3 class="mb-3">Daftar Peminjaman</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Peminjam</th>
                            <th>Alat yang Dipinjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Status</th>
                            <th width="260">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($peminjamans as $peminjaman)
                            <tr>
                                <td>
                                    {{ $peminjaman->user->name ?? '-' }}
                                </td>

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

                                <td>
                                    @if($peminjaman->status === 'diajukan')
                                        <form action="{{ route('petugas.peminjaman.setujui', $peminjaman->id) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                Setujui Peminjaman
                                            </button>
                                        </form>
                                    @elseif($peminjaman->status === 'dipinjam')
                                        <form action="{{ route('petugas.pengembalian.proses', $peminjaman->id) }}"
                                              method="POST">
                                            @csrf

                                            <input type="text"
                                                   name="kondisi_kembali"
                                                   class="form-control form-control-sm mb-2"
                                                   placeholder="Kondisi alat"
                                                   required>

                                            <input type="number"
                                                   name="denda"
                                                   class="form-control form-control-sm mb-2"
                                                   placeholder="Denda"
                                                   min="0"
                                                   value="0">

                                            <button type="submit" class="btn btn-primary btn-sm">
                                                Proses Pengembalian
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">Tidak ada aksi</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Belum ada data peminjaman.
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