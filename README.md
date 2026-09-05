# 🛍️ TokoKita - E-Commerce Laravel 10

Platform belanja online modern dibangun dengan Laravel 10, terinspirasi dari Shopee/Tokopedia.

## 🚀 Fitur Utama

- ✅ Login dengan Google OAuth 2.0 (tanpa password)
- ✅ Role System: Admin & Customer
- ✅ Katalog Produk dengan filter & sorting
- ✅ Keranjang Belanja dengan update AJAX
- ✅ Checkout & Manajemen Pesanan
- ✅ Wishlist & Review Produk
- ✅ Invoice PDF otomatis
- ✅ Dashboard Admin dengan Chart.js
- ✅ CRUD Produk, Kategori, Pesanan, User
- ✅ Desain Modern (Tailwind CSS + Alpine.js)
- ✅ Responsive Mobile-First

## 📦 Teknologi

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.1+, Laravel 10 |
| Frontend | Tailwind CSS (CDN), Alpine.js |
| Auth | Laravel Socialite + Google OAuth |
| Database | MySQL |
| PDF | barryvdh/laravel-dompdf |
| Charts | Chart.js |
| Icons | Font Awesome 6 |

## ⚙️ Instalasi

### 1. Clone & Install Dependencies

```bash
# Clone repository
git clone https://github.com/yourname/tokokita.git
cd tokokita

# Install PHP dependencies
composer install

# Install Node.js dependencies (opsional)
npm install
```

### 2. Konfigurasi Environment

```bash
# Copy .env
cp .env.example .env

# Generate app key
php artisan key:generate
```

Edit file `.env`:

```env
APP_NAME=TokoKita
APP_URL=http://localhost

DB_DATABASE=tokokita
DB_USERNAME=root
DB_PASSWORD=

# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URL=http://localhost/auth/google/callback

# Admin emails (pisahkan dengan koma)
ADMIN_EMAILS=youremail@gmail.com
```

### 3. Setup Google OAuth

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru
3. Enable **Google+ API** atau **Google Identity Services**
4. Buat **OAuth 2.0 Client ID**
5. Tambahkan Authorized Redirect URI: `http://localhost/auth/google/callback`
6. Copy Client ID & Client Secret ke `.env`

### 4. Buat Database & Migrasi

```bash
# Buat database MySQL bernama 'tokokita'
# kemudian jalankan:

php artisan migrate

# Isi dengan data dummy
php artisan db:seed
```

### 5. Storage Link

```bash
php artisan storage:link
```

### 6. Jalankan Server

```bash
# Menggunakan Laragon (rekomendasi)
# Cukup letakkan folder di C:/laragon/www/
# Buka http://tokokita.test

# Atau dengan artisan serve:
php artisan serve
```

## 👤 Akun Default Setelah Seeder

| Role | Email |
|------|-------|
| Admin | admin@tokokita.com |
| Customer | budi@example.com |

**Catatan:** Login menggunakan Google OAuth. Akun di atas hanya sebagai data dummy. Untuk menjadi admin, masukkan email Google Anda ke `ADMIN_EMAILS` di `.env`.

## 📁 Struktur Folder

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/GoogleController.php
│   │   ├── User/  (HomeController, ProductController, CartController, OrderController, ProfileController, ReviewController, WishlistController)
│   │   └── Admin/ (DashboardController, ProductController, CategoryController, OrderController, UserController)
│   └── Middleware/AdminMiddleware.php
├── Models/ (User, Product, Category, Cart, Order, OrderItem, Review, Wishlist)
database/
├── migrations/ (8 migration files)
└── seeders/DatabaseSeeder.php
resources/views/
├── layouts/ (app.blade.php, admin.blade.php)
├── components/ (product-card.blade.php)
├── user/ (home, products, cart, checkout, orders, profile, wishlist)
└── admin/ (dashboard, products, categories, orders, users)
routes/web.php
```

## 🔑 Environment Variables Penting

| Variable | Keterangan |
|----------|-----------|
| `GOOGLE_CLIENT_ID` | Client ID dari Google Cloud Console |
| `GOOGLE_CLIENT_SECRET` | Client Secret dari Google Cloud Console |
| `GOOGLE_REDIRECT_URL` | URL callback OAuth |
| `ADMIN_EMAILS` | Email yang akan otomatis jadi admin |

## 🛡️ Keamanan

- CSRF Protection pada semua form
- SQL Injection prevention (Eloquent ORM)
- XSS Prevention (Blade auto-escaping)
- Admin middleware untuk route protection
- Input validation di semua controller

## 📸 Halaman Utama

| URL | Deskripsi |
|-----|-----------|
| `/` | Landing page |
| `/products` | Daftar produk |
| `/products/{slug}` | Detail produk |
| `/cart` | Keranjang belanja |
| `/checkout` | Halaman checkout |
| `/orders` | Riwayat pesanan |
| `/profile` | Edit profil |
| `/wishlist` | Wishlist |
| `/auth/google` | Login Google |
| `/admin` | Dashboard admin |
| `/admin/products` | Kelola produk |
| `/admin/categories` | Kelola kategori |
| `/admin/orders` | Kelola pesanan |
| `/admin/users` | Kelola pengguna |

## 🔧 Optimasi Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

Dibuat dengan ❤️ menggunakan Laravel 10 + Tailwind CSS
