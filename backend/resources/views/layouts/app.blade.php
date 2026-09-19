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
            overscroll-behavior: contain;
        }

        .sidebar-shell.is-open .sidebar-panel {
            transform: translateX(0);
        }

        @media (min-width: 768px) {
            .sidebar-shell { width: 16rem; }
            .sidebar-shell:not(.is-open) { width: 0; }
            .sidebar-panel {
                position: sticky;
                top: 0;
                height: 100vh;
                transform: translateX(0);
            }
            .sidebar-shell:not(.is-open) .sidebar-panel {
                transform: translateX(-100%);
            }
        }

        @media print {
            html, body { min-height: 0 !important; height: auto !important; overflow: visible !important; }
            .no-print, #sidebar-shell, #sidebar-backdrop, header, .sidebar-panel { display: none !important; }
            body { background: white !important; }
            body > div.flex { display: block !important; min-height: 0 !important; }
            main { width: 100% !important; max-width: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-100 text-gray-800">
    <div class="flex min-h-screen">
    <div id="sidebar-shell" class="sidebar-shell is-open no-print">
            <aside id="sidebar" class="sidebar-panel no-print flex flex-col overflow-hidden bg-gray-800 text-gray-300 shadow-xl">
                <div class="border-b border-gray-700 px-6 py-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Sistem</p>
                    <h1 class="mt-1 text-xl font-bold text-white">Peminjaman Alat</h1>
                </div>

                <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-3 py-6">
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

                <div class="shrink-0 border-t border-gray-700 p-4">
                    <div class="relative mb-3">
                        <button id="profile-toggle" type="button" aria-controls="profile-menu" aria-expanded="false" class="flex w-full items-center gap-3 rounded-lg bg-gray-700 px-3 py-3 text-left transition hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400">
                            @if(auth()->user()->foto_profile)
                                <img src="{{ asset(auth()->user()->foto_profile) }}" alt="Foto {{ auth()->user()->name }}" class="h-10 w-10 shrink-0 rounded-full object-cover">
                            @else
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-500 text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</span>
                                <span class="mt-1 block text-xs uppercase tracking-wide text-gray-300">{{ auth()->user()->role }}</span>
                            </span>
                            <span class="text-xs text-gray-300">▴</span>
                        </button>

                        <div id="profile-menu" class="absolute bottom-full left-0 z-50 mb-2 hidden max-h-[calc(100vh-1rem)] w-full max-w-[calc(100vw-2rem)] overflow-y-auto rounded-xl border border-gray-200 bg-white p-4 text-gray-800 shadow-xl">
                            <div class="flex items-center gap-3 border-b border-gray-200 pb-4">
                                @if(auth()->user()->foto_profile)
                                    <img src="{{ asset(auth()->user()->foto_profile) }}" alt="Foto {{ auth()->user()->name }}" class="h-14 w-14 shrink-0 rounded-full object-cover">
                                @else
                                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gray-700 text-xl font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                @endif
                                <div class="min-w-0">
                                    <p class="truncate font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-sm text-gray-500">{{ auth()->user()->email }}</p>
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ auth()->user()->role }}</p>
                                </div>
                            </div>

                            <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="pt-4">
                                @csrf
                                <label for="foto_profile" class="mb-2 block text-sm font-semibold text-gray-700">Ganti foto profil</label>
                                <input id="foto_profile" name="foto_profile" type="file" accept="image/jpeg,image/png,image/webp" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-xs text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-700 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-gray-600">
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                                <button type="submit" class="mt-3 w-full rounded-lg bg-gray-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-gray-600">Simpan Foto</button>
                            </form>

                            <form action="{{ route('logout') }}" method="POST" class="mt-4 border-t border-gray-200 pt-3">
                                @csrf
                                <button type="submit" class="w-full rounded-lg bg-red-50 px-3 py-2 text-left text-sm font-semibold text-red-600 transition hover:bg-red-100">Keluar</button>
                            </form>
                        </div>
                    </div>
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
                    <div class="flex items-center gap-2">
                        @if(auth()->user()->foto_profile)
                            <img src="{{ asset(auth()->user()->foto_profile) }}" alt="Foto {{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover">
                        @else
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-700 text-sm font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        @endif
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs capitalize text-gray-500">{{ auth()->user()->role }}</p>
                        </div>
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
            const profileToggle = document.getElementById('profile-toggle');
            const profileMenu = document.getElementById('profile-menu');

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

            profileToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                const isOpen = !profileMenu.classList.contains('hidden');
                profileMenu.classList.toggle('hidden', isOpen);
                profileToggle.setAttribute('aria-expanded', String(!isOpen));
            });

            profileMenu.addEventListener('click', function (event) {
                event.stopPropagation();
            });

            document.addEventListener('click', function () {
                profileMenu.classList.add('hidden');
                profileToggle.setAttribute('aria-expanded', 'false');
            });
        });
    </script>
</body>
</html>
