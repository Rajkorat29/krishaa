<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CmsController as AdminCmsController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InboxController as AdminInboxController;
use App\Http\Controllers\Admin\MetalController as AdminMetalController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Daisy Storefront Routes
|--------------------------------------------------------------------------
*/

// 1. Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Products & Catalogue
Route::get('/rings', [ProductController::class, 'rings'])->name('rings');
Route::get('/earrings', [ProductController::class, 'earrings'])->name('earrings');
Route::get('/bangles', [ProductController::class, 'bangles'])->name('bangles');
Route::get('/necklaces', [ProductController::class, 'necklaces'])->name('necklaces');
Route::get('/pearls', [ProductController::class, 'pearls'])->name('pearls');
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{categorySlug}', [ProductController::class, 'index'])->name('shop.category');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// 3. Wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// 4. Shopping Bag / Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');

// 5. Static Pages
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::get('/visit-custom', [PageController::class, 'visitCustom'])->name('pages.visit-custom');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');

// 6. Contact & Newsletter Actions
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

/*
|--------------------------------------------------------------------------
| Authenticated Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order-confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    // Customer Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Orders in Account
    Route::get('/account/orders', [CustomerOrderController::class, 'index'])->name('account.orders');
    Route::get('/account/orders/{orderNumber}', [CustomerOrderController::class, 'show'])->name('account.orders.show');
    Route::post('/account/orders/{orderNumber}/cancel', [CustomerOrderController::class, 'cancel'])->name('account.orders.cancel');

    // Saved Addresses
    Route::get('/account/addresses', [AddressController::class, 'index'])->name('account.addresses');
    Route::post('/account/addresses', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::delete('/account/addresses/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');

    // Profile & Dashboard
    Route::get('/dashboard', function () {
        if (auth()->user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('account.orders');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('account.profile');
    Route::patch('/account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (/admin) - Protected by EnsureUserIsAdmin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products CRUD & Trash
    Route::post('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{id}/force', [AdminProductController::class, 'forceDelete'])->name('products.forceDelete');
    Route::resource('products', AdminProductController::class);

    // Categories & Metals CRUD
    Route::resource('categories', AdminCategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('metals', AdminMetalController::class)->except(['create', 'show', 'edit']);

    // Orders Management & Invoice
    Route::get('/orders/export', [AdminOrderController::class, 'exportCsv'])->name('orders.export');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/note', [AdminOrderController::class, 'addNote'])->name('orders.note');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');

    // Customers
    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::patch('/customers/{user}/block', [AdminCustomerController::class, 'toggleBlock'])->name('customers.block');

    // Reviews
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'toggleApprove'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Coupons
    Route::resource('coupons', AdminCouponController::class)->except(['create', 'show', 'edit']);

    // Homepage CMS
    Route::get('/cms', [AdminCmsController::class, 'index'])->name('cms.index');
    Route::post('/cms/hero', [AdminCmsController::class, 'saveHero'])->name('cms.hero.save');
    Route::delete('/cms/hero/{slide}', [AdminCmsController::class, 'deleteHero'])->name('cms.hero.delete');
    Route::post('/cms/drop', [AdminCmsController::class, 'saveDrop'])->name('cms.drop.save');
    Route::delete('/cms/drop/{drop}', [AdminCmsController::class, 'deleteDrop'])->name('cms.drop.delete');
    Route::post('/cms/social', [AdminCmsController::class, 'saveSocial'])->name('cms.social.save');
    Route::delete('/cms/social/{post}', [AdminCmsController::class, 'deleteSocial'])->name('cms.social.delete');
    Route::post('/cms/marquee', [AdminCmsController::class, 'saveMarquee'])->name('cms.marquee.save');
    Route::delete('/cms/marquee/{word}', [AdminCmsController::class, 'deleteMarquee'])->name('cms.marquee.delete');

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Inbox & Newsletter
    Route::get('/inbox', [AdminInboxController::class, 'index'])->name('inbox.index');
    Route::patch('/inbox/{message}/read', [AdminInboxController::class, 'markRead'])->name('inbox.read');
    Route::get('/inbox/export-messages', [AdminInboxController::class, 'exportMessages'])->name('inbox.exportMessages');
    Route::get('/inbox/export-subscribers', [AdminInboxController::class, 'exportSubscribers'])->name('inbox.exportSubscribers');
});

// Storage fallback route for Windows environments where symlink creation is restricted
Route::get('/storage/{path}', function (string $path) {
    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }
    $mimeType = Storage::disk('public')->mimeType($path) ?: 'application/octet-stream';
    return Response::make(Storage::disk('public')->get($path), 200, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.local');

require __DIR__.'/auth.php';
