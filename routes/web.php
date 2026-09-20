<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderLookupController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');
Route::get('/tim-kiem/goi-y', [SearchController::class, 'suggest'])
    ->middleware('throttle:60,1')
    ->name('search.suggest');

Route::get('/so-sanh', [CompareController::class, 'show'])->name('compare.show');

Route::get('/tin-tuc', [PostController::class, 'index'])->name('posts.index');
Route::get('/tin-tuc/{post}', [PostController::class, 'show'])->name('posts.show');

Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::post('/gio-hang', [CartController::class, 'store'])->name('cart.store');
Route::patch('/gio-hang/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/gio-hang/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/gio-hang/ma-giam-gia', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/gio-hang/ma-giam-gia', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Không yêu cầu đăng nhập — khách vãng lai vẫn thanh toán được.
Route::get('/thanh-toan', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/thanh-toan', [CheckoutController::class, 'store'])->name('checkout.store');

// Tra cứu đơn hàng bằng mã + số điện thoại, dành cho khách không có tài
// khoản. Throttle vì mã đơn + số điện thoại là "mật khẩu" duy nhất ở đây.
Route::get('/don-hang/tra-cuu', [OrderLookupController::class, 'create'])->name('orders.lookup');
Route::post('/don-hang/tra-cuu', [OrderLookupController::class, 'show'])
    ->middleware('throttle:10,1')
    ->name('orders.lookup.show');

// Link xác nhận một lần sau khi khách vãng lai đặt hàng xong — chữ ký là
// bằng chứng sở hữu duy nhất, không cần đăng nhập.
Route::get('/don-hang/xac-nhan/{order}', [OrderLookupController::class, 'confirmation'])
    ->middleware('signed')
    ->name('orders.guest-show');

Route::get('/dich-vu', [PageController::class, 'services'])->name('pages.services');
Route::get('/tra-gop', [PageController::class, 'installment'])->name('pages.installment');
Route::get('/thu-cu-doi-moi', [PageController::class, 'tradeIn'])->name('pages.trade-in');
Route::get('/chinh-sach', [PageController::class, 'policies'])->name('pages.policies');
Route::get('/chinh-sach/bao-hanh', [PageController::class, 'warrantyPolicy'])->name('pages.policies.warranty');
Route::get('/chinh-sach/doi-tra', [PageController::class, 'returnsPolicy'])->name('pages.policies.returns');
Route::get('/chinh-sach/van-chuyen', [PageController::class, 'shippingPolicy'])->name('pages.policies.shipping');
Route::get('/chinh-sach/bao-mat', [PageController::class, 'privacyPolicy'])->name('pages.policies.privacy');
Route::get('/gioi-thieu', [PageController::class, 'about'])->name('pages.about');
Route::get('/lien-he', [PageController::class, 'contact'])->name('pages.contact');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');

Route::get('/vnpay/return', [VnpayController::class, 'return'])->name('vnpay.return');
Route::get('/vnpay/ipn', [VnpayController::class, 'ipn'])->name('vnpay.ipn');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::post('/san-pham/{product}/danh-gia', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/yeu-thich', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/yeu-thich/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/yeu-thich/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
