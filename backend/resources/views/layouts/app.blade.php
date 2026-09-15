<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Peminjaman Alat')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 text-gray-800">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 bg-gray-800 text-gray-300 md:flex md:flex-col">
            <div class="border-b border-gray-700 px-6 py-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Sistem</p>
                <h1 class="mt-1 text-xl font-bold text-white">Peminjaman Alat</h1>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-6">
                <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Menu Utama</p>

                <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <span class="mr-3 text-base">▦</span> Dashboard
                </a>
                <a href="{{ route('admin.user.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.user.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <span class="mr-3 text-base">♙</span> Kelola User
                </a>
                <a href="{{ route('admin.kategori.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.kategori.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <span class="mr-3 text-base">▤</span> Kelola Kategori
                </a>
                <a href="{{ route('admin.alat.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.alat.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <span class="mr-3 text-base">▣</span> Kelola Alat
                </a>
                <a href="{{ route('admin.peminjaman.index') }}" class="flex items-center rounded-lg px-3 py-3 text-sm font-medium transition {{ request()->routeIs('admin.peminjaman.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}">
                    <span class="mr-3 text-base">▱</span> Data Peminjaman
                </a>
            </nav>

            <div class="border-t border-gray-700 p-4">
                <div class="mb-3 rounded-lg bg-gray-700 px-3 py-3">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="mt-1 text-xs uppercase tracking-wide text-gray-300">{{ auth()->user()->role }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-gray-300 transition hover:bg-gray-700 hover:text-white">Keluar</button>
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <header class="border-b border-gray-200 bg-white">
                <div class="flex min-h-20 items-center justify-between px-5 py-4 sm:px-8">
                    <div>
                        <p class="text-sm text-gray-500">Panel Administrasi</p>
                        <h2 class="text-xl font-bold text-gray-900">@yield('header-title', 'Dashboard')</h2>
                    </div>
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs capitalize text-gray-500">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </header>

            <main class="mx-auto w-full max-w-7xl p-5 sm:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
