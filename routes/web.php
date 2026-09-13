<?php

use App\Http\Controllers\BuyNowController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VnpayController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang', [CartController::class, 'store'])->name('cart.store');
Route::patch('/gio-hang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/gio-hang/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::post('/mua-ngay', [BuyNowController::class, 'store'])->name('buy-now.store');

Route::get('/dich-vu', [PageController::class, 'services'])->name('pages.services');
Route::get('/chinh-sach', [PageController::class, 'policies'])->name('pages.policies');
Route::get('/chinh-sach/bao-hanh', [PageController::class, 'warrantyPolicy'])->name('pages.policies.warranty');
Route::get('/chinh-sach/doi-tra', [PageController::class, 'returnsPolicy'])->name('pages.policies.returns');
Route::get('/chinh-sach/van-chuyen', [PageController::class, 'shippingPolicy'])->name('pages.policies.shipping');
Route::get('/chinh-sach/bao-mat', [PageController::class, 'privacyPolicy'])->name('pages.policies.privacy');
Route::get('/gioi-thieu', [PageController::class, 'about'])->name('pages.about');
Route::get('/lien-he', [PageController::class, 'contact'])->name('pages.contact');

Route::get('/vnpay/return', [VnpayController::class, 'return'])->name('vnpay.return');
Route::get('/vnpay/ipn', [VnpayController::class, 'ipn'])->name('vnpay.ipn');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order}', [OrderController::class, 'show'])->name('orders.show');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
