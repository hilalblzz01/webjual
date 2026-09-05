<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - @yield('title', 'Dashboard') | TokoKita</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#FF6B00', 50: '#FFF0E6', 100: '#FFD4B2', 500: '#FF6B00', 600: '#E65F00' },
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        html, body { overflow-x: hidden; width: 100%; font-family: 'Inter', sans-serif; }
        img { max-width: 100%; height: auto; }
        .gradient-primary { background: linear-gradient(135deg, #FF6B00, #FF9F43); }
        .sidebar-link { transition: all 0.2s ease; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(255,107,0,0.1); color: #FF6B00; }
        .sidebar-link.active { border-left: 3px solid #FF6B00; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #FF6B00; border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased overflow-x-hidden" x-data="{ sidebarOpen: false }">

<!-- Mobile Sidebar Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak></div>

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="fixed md:relative inset-y-0 left-0 z-50 md:z-auto w-64 bg-gray-900 text-gray-300 flex flex-col transition-transform duration-300"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-800">
            <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-secondary rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-bag text-white text-sm"></i>
            </div>
            <div>
                <p class="font-bold text-white text-base leading-none">TokoKita</p>
                <p class="text-xs text-primary-DEFAULT">Admin Panel</p>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'active text-primary-500 bg-primary-50' : '' }}">
                <i class="fas fa-tachometer-alt w-5 text-center"></i> Dashboard
            </a>

            <p class="text-xs uppercase text-gray-500 font-semibold px-3 pt-4 pb-1">Katalog</p>

            <a href="{{ route('admin.products.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.products*') ? 'active text-primary-500 bg-primary-50' : '' }}">
                <i class="fas fa-box w-5 text-center"></i> Produk
            </a>
            <a href="{{ route('admin.digital-stock.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.digital-stock*') ? 'active text-primary-500 bg-primary-50' : '' }}">
                <i class="fas fa-key w-5 text-center text-emerald-500"></i> Stok Akun Digital
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.categories*') ? 'active text-primary-500 bg-primary-50' : '' }}">
                <i class="fas fa-tags w-5 text-center"></i> Kategori
            </a>

            <p class="text-xs uppercase text-gray-500 font-semibold px-3 pt-4 pb-1">Transaksi</p>

            <a href="{{ route('admin.orders.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.orders*') ? 'active text-primary-500 bg-primary-50' : '' }}">
                <i class="fas fa-shopping-bag w-5 text-center"></i> Pesanan
                @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                <span class="ml-auto bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">{{ $pendingCount }}</span>
                @endif
            </a>

            <p class="text-xs uppercase text-gray-500 font-semibold px-3 pt-4 pb-1">Pengguna & Pengaturan</p>

            <a href="{{ route('admin.users.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users*') ? 'active text-primary-500 bg-primary-50' : '' }}">
                <i class="fas fa-users w-5 text-center"></i> Pengguna
            </a>
            <a href="{{ route('admin.settings.index') }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('admin.settings*') ? 'active text-primary-500 bg-primary-50' : '' }}">
                <i class="fas fa-qrcode w-5 text-center text-amber-400"></i> Pengaturan QRIS
            </a>
        </nav>

        <!-- User Info -->
        <div class="border-t border-gray-800 p-4">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-primary-DEFAULT">Administrator</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-red-400 transition-colors" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4">
            <button @click="sidebarOpen = true" class="md:hidden text-gray-600">
                <i class="fas fa-bars text-lg"></i>
            </button>

            <div>
                <h1 class="text-lg font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
                <p class="text-xs text-gray-500">@yield('breadcrumb', 'Admin Panel')</p>
            </div>

            <div class="ml-auto flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-500 hover:text-primary-500 transition-colors">
                    <i class="fas fa-external-link-alt mr-1"></i> Lihat Toko
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6">

            <!-- Alerts -->
            @if(session('success'))
            <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3" x-data="{ show: true }" x-show="show">
                <i class="fas fa-check-circle"></i>
                <span class="text-sm flex-1">{{ session('success') }}</span>
                <button @click="show = false"><i class="fas fa-times text-xs"></i></button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3" x-data="{ show: true }" x-show="show">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="text-sm flex-1">{{ session('error') }}</span>
                <button @click="show = false"><i class="fas fa-times text-xs"></i></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

@stack('scripts')
</body>
</html>
