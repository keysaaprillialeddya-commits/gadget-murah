<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Import Controllers
use App\Http\Controllers\{
    HomeController,
    CatalogController,
    CartController,
    CheckoutController,
    WishlistController,
    OrderController,
    ProfileController,
    PaymentController,
    MidtransNotificationController
};
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\{
    DashboardController,
    ProductController as AdminProductController,
    CategoryController as AdminCategoryController,
    OrderController as AdminOrderController
};

// ================================================
// PUBLIC ROUTES
// ================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

// Google Auth
Route::controller(GoogleController::class)->group(function () {
    Route::get('/auth/google', 'redirect')->name('auth.google');
    Route::get('/auth/google/callback', 'callback')->name('auth.google.callback');
});

// Midtrans Webhook (Public)
Route::post('midtrans/notification', [MidtransNotificationController::class, 'handle'])
    ->name('midtrans.notification');

// ================================================
// CUSTOMER ROUTES (Auth Required)
// ================================================
Route::middleware('auth')->group(function () {
    // Cart
    Route::prefix('cart')->name('cart.')->group(function() {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/{item}', [CartController::class, 'update'])->name('update');
        Route::delete('/{item}', [CartController::class, 'remove'])->name('remove');
    });

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders & Payment
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/pay', [PaymentController::class, 'show'])->name('orders.pay');
    Route::get('/orders/{order}/success', [PaymentController::class, 'success'])->name('orders.success');
    Route::get('/orders/{order}/pending', [PaymentController::class, 'pending'])->name('orders.pending');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // PERBAIKAN: Tambahkan rute hapus avatar di sini
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

// ================================================
// ADMIN ROUTES (Auth & Admin Middleware)
// ================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // CRUD Resources
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
});

// Auth Default (Login, Register, Logout)
Auth::routes();



Route::middleware('auth')->group(function () {
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
});
