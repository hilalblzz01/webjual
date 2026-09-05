<!DOCTYPE html>
<html lang="id" class="scroll-smooth overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'TokoKita')) - Belanja Online Terpercaya</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#FF6B00', 50: '#FFF0E6', 100: '#FFD4B2', 500: '#FF6B00', 600: '#E65F00', 700: '#CC5400' },
                        secondary: '#FF9F43',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        html, body { overflow-x: hidden; width: 100%; font-family: 'Inter', sans-serif; background-color: #F8F9FA; }
        img { max-width: 100%; height: auto; }
        .gradient-primary { background: linear-gradient(135deg, #FF6B00, #FF9F43); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
        .toast { animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .product-img { transition: transform 0.4s ease; }
        .product-img:hover { transform: scale(1.05); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #FF6B00; border-radius: 3px; }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col justify-between overflow-x-hidden antialiased text-gray-800" x-data="{ cartCount: 0, mobileMenu: false }" x-init="fetch('/cart/count').then(r=>r.json()).then(d=>cartCount=d.count).catch(()=>{})">

<!-- ===== NAVBAR ===== -->
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-3 sm:px-4">
        <div class="flex items-center h-16 gap-2 sm:gap-4">

            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                <div class="w-8 h-8 sm:w-9 sm:h-9 gradient-primary rounded-xl flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-white text-xs sm:text-sm"></i>
                </div>
                <span class="text-lg sm:text-xl font-bold text-gray-800">Toko<span class="text-primary">Kita</span></span>
            </a>

            <!-- Search Bar (Desktop) -->
            <form action="{{ route('products.index') }}" method="GET" class="hidden md:flex flex-1 max-w-xl">
                <div class="flex w-full rounded-xl overflow-hidden border border-gray-200 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-50 transition-all">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari produk digital..."
                           class="flex-1 px-4 py-2.5 text-sm outline-none bg-white">
                    <button type="submit" class="px-4 gradient-primary text-white hover:opacity-90 transition-opacity">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                </div>
            </form>

            <!-- Right Nav -->
            <div class="flex items-center gap-2 sm:gap-3 ml-auto">
                <!-- Wishlist -->
                @auth
                <a href="{{ route('wishlist.index') }}" class="hidden sm:flex items-center gap-1.5 text-gray-600 hover:text-primary-500 transition-colors p-2">
                    <i class="far fa-heart text-lg"></i>
                </a>
                @endauth

                <!-- Cart -->
                <a href="{{ auth()->check() ? route('cart.index') : route('auth.google') }}"
                   class="relative flex items-center gap-1.5 text-gray-700 hover:text-primary-500 transition-colors p-2">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <span x-show="cartCount > 0"
                          x-text="cartCount"
                          class="absolute -top-1 -right-1 bg-primary-500 text-white text-[10px] w-4 h-4 sm:w-5 sm:h-5 rounded-full flex items-center justify-center font-bold"></span>
                </a>

                <!-- Auth (Desktop) -->
                @auth
                <div class="relative hidden sm:block" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-50 transition-colors">
                        <img src="{{ auth()->user()->avatar_url }}" alt="Avatar"
                             class="w-8 h-8 rounded-full object-cover border-2 border-primary-100">
                        <span class="text-sm font-medium text-gray-700 max-w-[100px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">

                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                            <i class="fas fa-tachometer-alt w-4 text-center"></i>
                            <span>Admin Panel</span>
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        @endif

                        <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-box w-4 text-center"></i>
                            <span>Pesanan Saya</span>
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="far fa-heart w-4 text-center"></i>
                            <span>Wishlist</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user w-4 text-center"></i>
                            <span>Edit Profil</span>
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="hidden sm:flex items-center gap-2">
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 border border-primary-500 text-primary-500 text-sm font-semibold rounded-xl hover:bg-primary-50 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-3.5 py-1.5 gradient-primary text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity">
                        Daftar
                    </a>
                </div>
                @endauth

                <!-- Mobile Menu Toggle Button -->
                <button @click="mobileMenu = true" class="md:hidden p-2 text-gray-700 hover:text-primary-500 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="md:hidden pb-3">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="flex rounded-xl overflow-hidden border border-gray-200 focus-within:border-primary-500 transition-all">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari produk..."
                           class="flex-1 px-3 py-2 text-xs outline-none bg-white">
                    <button type="submit" class="px-3 gradient-primary text-white">
                        <i class="fas fa-search text-xs"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Category Nav Horizontal Scrollable (Desktop & Mobile) -->
        <div class="flex items-center gap-4 sm:gap-6 pb-2 text-xs sm:text-sm overflow-x-auto scrollbar-hide border-t border-gray-50 pt-2">
            <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-primary-500 font-semibold whitespace-nowrap transition-colors {{ request()->routeIs('products.index') && !request('category') ? 'text-primary-500 font-bold border-b-2 border-primary-500 pb-1' : '' }}">
                🔥 Semua Produk
            </a>
            @foreach(\App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->limit(10)->get() as $cat)
            <a href="{{ route('categories.show', $cat->slug) }}" class="text-gray-600 hover:text-primary-500 font-medium whitespace-nowrap transition-colors {{ request()->fullUrlIs(route('categories.show', $cat->slug)) ? 'text-primary-500 font-bold border-b-2 border-primary-500 pb-1' : '' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
</nav>

<!-- ===== MOBILE MENU SLIDE-OVER DRAWER ===== -->
<div x-show="mobileMenu"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex justify-end md:hidden" style="display: none;">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="mobileMenu = false"></div>

    <!-- Drawer Content -->
    <div class="relative w-4/5 max-w-xs bg-white h-full shadow-2xl flex flex-col z-10 overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b border-gray-100 gradient-primary text-white">
            <div class="flex items-center gap-2">
                <i class="fas fa-shopping-bag text-white"></i>
                <span class="font-bold text-lg">Menu Navigation</span>
            </div>
            <button @click="mobileMenu = false" class="text-white hover:text-gray-200 text-lg p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>

        @auth
        <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center gap-3">
            <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full object-cover border-2 border-primary-500">
            <div class="min-w-0">
                <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
        @endauth

        <div class="p-4 space-y-2 flex-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                <i class="fas fa-home w-5 text-center text-primary-500"></i> Beranda
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                <i class="fas fa-th-large w-5 text-center text-primary-500"></i> Semua Produk
            </a>

            @auth
            <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                <i class="fas fa-box-open w-5 text-center text-primary-500"></i> Pesanan Saya
            </a>
            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                <i class="fas fa-heart w-5 text-center text-primary-500"></i> Wishlist
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-primary-50 hover:text-primary-600">
                <i class="fas fa-user-cog w-5 text-center text-primary-500"></i> Edit Profil
            </a>
            @if(auth()->user()->isAdmin())
            <div class="border-t border-gray-100 my-2 pt-2"></div>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-amber-600 bg-amber-50">
                <i class="fas fa-user-shield w-5 text-center"></i> Admin Panel
            </a>
            @endif
            @endauth

            <div class="border-t border-gray-100 my-2 pt-2">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2 px-3">Kategori Produk</p>
                @foreach(\App\Models\Category::where('is_active', true)->whereNull('parent_id')->orderBy('sort_order')->get() as $c)
                <a href="{{ route('categories.show', $c->slug) }}" class="flex items-center justify-between px-3 py-2 text-xs font-medium text-gray-600 hover:text-primary-500">
                    <span>{{ $c->name }}</span>
                    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                </a>
                @endforeach
            </div>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50">
            @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2.5 bg-red-50 text-red-600 rounded-xl text-sm font-bold flex items-center justify-center gap-2 hover:bg-red-100 transition-colors">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
            @else
            <div class="space-y-2">
                <a href="{{ route('login') }}" class="block w-full py-2.5 text-center border-2 border-primary-500 text-primary-500 font-bold text-sm rounded-xl hover:bg-primary-50">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="block w-full py-2.5 text-center gradient-primary text-white font-bold text-sm rounded-xl shadow-md">
                    Daftar Akun Baru
                </a>
            </div>
            @endauth
        </div>
    </div>
</div>

<!-- Toast Notifications -->
<div class="fixed top-20 right-4 z-50 space-y-2" id="toast-container">
    @if(session('success'))
    <div class="toast flex items-center gap-3 bg-white border-l-4 border-green-500 rounded-xl shadow-lg p-4 max-w-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check text-green-500 text-sm"></i>
        </div>
        <p class="text-sm text-gray-700 flex-1">{{ session('success') }}</p>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
    </div>
    @endif

    @if(session('error'))
    <div class="toast flex items-center gap-3 bg-white border-l-4 border-red-500 rounded-xl shadow-lg p-4 max-w-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
        </div>
        <p class="text-sm text-gray-700 flex-1">{{ session('error') }}</p>
        <button @click="show = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xs"></i></button>
    </div>
    @endif
</div>

<!-- Main Content -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-gray-900 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 gradient-primary rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-white">Toko<span class="text-primary-DEFAULT">Kita</span></span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">Platform belanja online terpercaya dengan produk berkualitas dan harga terbaik.</p>
                <div class="flex gap-3 mt-4">
                    <a href="#" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-500 transition-colors">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-500 transition-colors">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary-500 transition-colors">
                        <i class="fab fa-tiktok text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Links -->
            <div>
                <h4 class="font-semibold text-white mb-4">Produk</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('products.index') }}" class="hover:text-primary-DEFAULT transition-colors">Semua Produk</a></li>
                    <li><a href="{{ route('products.index') }}?sort=popular" class="hover:text-primary-DEFAULT transition-colors">Terlaris</a></li>
                    <li><a href="{{ route('products.index') }}?sort=latest" class="hover:text-primary-DEFAULT transition-colors">Terbaru</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4">Akun</h4>
                <ul class="space-y-2 text-sm">
                    @auth
                    <li><a href="{{ route('orders.index') }}" class="hover:text-primary-DEFAULT transition-colors">Pesanan Saya</a></li>
                    <li><a href="{{ route('profile.edit') }}" class="hover:text-primary-DEFAULT transition-colors">Edit Profil</a></li>
                    <li><a href="{{ route('wishlist.index') }}" class="hover:text-primary-DEFAULT transition-colors">Wishlist</a></li>
                    @else
                    <li><a href="{{ route('auth.google') }}" class="hover:text-primary-DEFAULT transition-colors">Login</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4">Bantuan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-primary-DEFAULT transition-colors">Cara Berbelanja</a></li>
                    <li><a href="#" class="hover:text-primary-DEFAULT transition-colors">Cara Pembayaran</a></li>
                    <li><a href="#" class="hover:text-primary-DEFAULT transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-primary-DEFAULT transition-colors">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-gray-500">© {{ date('Y') }} TokoKita. All rights reserved.</p>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span><i class="fas fa-lock text-green-400 mr-1"></i>SSL Secured</span>
                <span><i class="fas fa-shield-alt text-blue-400 mr-1"></i>100% Aman</span>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
