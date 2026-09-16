<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Peminjaman Alat')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }

        .sidebar-shell {
            width: 0;
            flex-shrink: 0;
            overflow: visible;
            transition: width 300ms ease-in-out;
        }

        .sidebar-panel {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 40;
            width: 16rem;
            transform: translateX(-100%);
            transition: transform 300ms ease-in-out;
        }

        .sidebar-shell.is-open .sidebar-panel {
            transform: translateX(0);
        }

        @media (min-width: 768px) {
            .sidebar-shell { width: 16rem; }
            .sidebar-shell:not(.is-open) { width: 0; }
            .sidebar-panel {
                position: relative;
                height: 100vh;
                transform: translateX(0);
            }
            .sidebar-shell:not(.is-open) .sidebar-panel {
                transform: translateX(-100%);
            }
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            main { max-width: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 text-gray-800">
    <div class="flex min-h-screen">
    <div id="sidebar-shell" class="sidebar-shell is-open no-print">
            <aside id="sidebar" class="sidebar-panel no-print flex flex-col overflow-y-auto bg-gray-800 text-gray-300 shadow-xl">
                <div class="border-b border-gray-700 px-6 py-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Sistem</p>
                    <h1 class="mt-1 text-xl font-bold text-white">Peminjaman Alat</h1>
                </div>

                <nav class="flex-1 space-y-1 px-3 py-6">
                    <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Menu Utama</p>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▦</span> Dashboard
                        </a>
                        <a href="{{ route('admin.user.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.user.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">♙</span> Kelola User
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.kategori.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▤</span> Kelola Kategori
                        </a>
                        <a href="{{ route('admin.alat.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.alat.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▣</span> Kelola Alat
                        </a>
                        <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.peminjaman.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▱</span> Data Peminjaman
                        </a>
                        <a href="{{ route('admin.pengembalian.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.pengembalian.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">↩</span> Kelola Pengembalian
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.laporan.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▧</span> Cetak Laporan
                        </a>
                    @elseif(auth()->user()->role === 'petugas')
                        <a href="{{ route('petugas.peminjaman.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('petugas.peminjaman.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▱</span> Persetujuan Peminjaman
                        </a>
                        <a href="{{ route('petugas.laporan.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('petugas.laporan.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▧</span> Cetak Laporan
                        </a>
                    @elseif(auth()->user()->role === 'peminjam')
                        <a href="{{ route('peminjam.katalog') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('peminjam.katalog') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▣</span> Katalog Alat
                        </a>
                        <a href="{{ route('peminjam.riwayat') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('peminjam.riwayat') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                            <span class="mr-3">▤</span> Riwayat Peminjaman
                        </a>
                    @endif
                </nav>

                <div class="border-t border-gray-700 p-4">
                    <div class="mb-3 rounded-lg bg-gray-700 px-3 py-3">
                        <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-300">{{ auth()->user()->role }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full rounded-lg border border-gray-600 bg-gray-600 px-3 py-2 text-left text-sm font-medium text-white transition hover:bg-gray-500">Keluar</button>
                    </form>
                </div>
            </aside>
        </div>

        <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>

        <div class="min-w-0 flex-1">
            <header class="no-print border-b border-gray-200 bg-white">
                <div class="flex min-h-20 items-center justify-between px-4 py-4 sm:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="sidebar-toggle" type="button" aria-controls="sidebar" aria-expanded="true" title="Sembunyikan sidebar" class="shrink-0 rounded-lg border border-gray-300 bg-white px-3 py-2 text-lg leading-none text-gray-700 transition hover:bg-gray-100">
                            ☰
                        </button>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-500">Panel Administrasi</p>
                            <h2 class="truncate text-lg font-bold text-gray-900 sm:text-xl">@yield('header-title', 'Dashboard')</h2>
                        </div>
                    </div>
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs capitalize text-gray-500">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-7xl p-4 sm:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const shell = document.getElementById('sidebar-shell');
            const toggle = document.getElementById('sidebar-toggle');
            const backdrop = document.getElementById('sidebar-backdrop');

            function isDesktop() {
                return window.matchMedia('(min-width: 768px)').matches;
            }

            function setSidebar(open) {
                shell.classList.toggle('is-open', open);
                backdrop.classList.toggle('hidden', !open || isDesktop());
                toggle.setAttribute('aria-expanded', String(open));
                toggle.setAttribute('title', open ? 'Sembunyikan sidebar' : 'Tampilkan sidebar');
            }

            setSidebar(isDesktop());

            toggle.addEventListener('click', function () {
                setSidebar(!shell.classList.contains('is-open'));
            });

            backdrop.addEventListener('click', function () {
                setSidebar(false);
            });

            window.addEventListener('resize', function () {
                setSidebar(isDesktop());
            });
        });
    </script>
</body>
</html>
