<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// ===== PUBLIC ROUTES =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categories/{slug}', [ProductController::class, 'category'])->name('categories.show');

// ===== AUTH ROUTES =====
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Google OAuth
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// ===== AUTHENTICATED USER ROUTES =====
Route::middleware(['auth'])->group(function () {
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

    // Checkout & Orders (3-Step Wizard Flow)
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    Route::get('/orders/{id}/chatroom', [OrderController::class, 'chatRoom'])->name('orders.chatroom');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{id}/proof', [OrderController::class, 'uploadProof'])->name('orders.proof');
    Route::post('/orders/{id}/chat', [OrderController::class, 'sendChat'])->name('orders.chat');

    // Review
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// ===== ADMIN ROUTES =====
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', Admin\ProductController::class);
    Route::get('digital-stock', [Admin\ProductController::class, 'digitalStock'])->name('digital-stock.index');
    Route::post('products/{product}/restock', [Admin\ProductController::class, 'restock'])->name('products.restock');
    Route::delete('digital-items/{id}', [Admin\ProductController::class, 'destroyDigitalItem'])->name('digital-items.destroy');

    // Categories
    Route::resource('categories', Admin\CategoryController::class);

    // Orders
    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{id}', [Admin\OrderController::class, 'update'])->name('orders.update');
    Route::post('orders/{id}/chat', [Admin\OrderController::class, 'sendChat'])->name('orders.chat');
    Route::get('orders/{id}/invoice', [Admin\OrderController::class, 'invoice'])->name('orders.invoice');

    // Users
    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::put('users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/toggle', [Admin\UserController::class, 'toggleStatus'])->name('users.toggle');

    // Settings (QRIS & Store Config)
    Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});
