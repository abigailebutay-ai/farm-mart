<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCouponController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CompletionProofController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentProofController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'public.about')->name('about');
Route::get('/marketplace', [ProductController::class, 'index'])->name('marketplace');
Route::get('/products', [ProductController::class, 'redirectToMarketplace'])->name('products.index');
Route::get('/product-image/{path}', [ProductImageController::class, 'show'])
    ->where('path', '.*')
    ->name('product.image');

Route::middleware(['auth', 'role:farmer'])->group(function () {
    Route::get('/products/create', fn () => redirect()->route('farmer.products.create'));
    Route::get('/products/{product}/edit', fn (App\Models\Product $product) => redirect()->route('farmer.products.edit', $product))
        ->whereNumber('product');
});

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->whereNumber('product')
    ->name('products.show');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/payment-proof/{path}', [PaymentProofController::class, 'show'])
        ->where('path', '.*')
        ->name('payment.proof');
    Route::get('/completion-proof/{path}', [CompletionProofController::class, 'show'])
        ->where('path', '.*')
        ->name('completion.proof');

    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:admin,farmer,consumer,buyer')
        ->name('dashboard');
    
    // Account routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePassword'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Cart routes (consumers only)
    Route::middleware('role:consumer,buyer')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount'])->name('cart.apply-discount');
        Route::delete('/cart/remove-discount', [CartController::class, 'removeDiscount'])->name('cart.remove-discount');
        Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::put('/cart-item/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.update-quantity');
        Route::delete('/cart-item/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    });

    // Role-scoped order routes
    Route::middleware('role:admin,farmer,consumer,buyer')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    // Checkout routes (consumers only)
    Route::middleware('role:consumer,buyer')->group(function () {
        Route::get('/checkout', [OrderController::class, 'showCheckout'])->name('checkout.show');
        Route::get('/checkout/farmer/{farmer}', [OrderController::class, 'showCheckoutForFarmer'])->name('checkout.farmer');
        Route::post('/checkout/coupon', [OrderController::class, 'applyCoupon'])->name('checkout.coupon.apply');
        Route::delete('/checkout/coupon', [OrderController::class, 'removeCoupon'])->name('checkout.coupon.remove');
        Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.store');
    });

    // Update order status (farmers only)
    Route::middleware('role:farmer')->group(function () {
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });
});

// Consumer routes
Route::middleware(['auth', 'role:consumer,buyer'])->prefix('consumer')->name('consumer.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
    Route::get('/marketplace', [ProductController::class, 'consumerMarketplace'])->name('marketplace');
    Route::get('/marketplace/farmer/{farmer}', [ProductController::class, 'consumerFarmerProducts'])->name('marketplace.farmer');
    Route::get('/cart', fn () => redirect()->route('cart.index'))->name('cart');
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/orders', fn () => redirect()->route('orders.index'))->name('orders');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancelByConsumer'])->name('orders.cancel');
    Route::get('/purchase-history', [OrderController::class, 'purchaseHistory'])->name('purchase-history');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');
    Route::get('/{path}', fn () => redirect()->route('dashboard'))->where('path', '.*')->name('fallback');
});

// Buyer routes mirror consumer access for copied buyer-prefixed URLs.
Route::middleware(['auth', 'role:consumer,buyer'])->prefix('buyer')->name('buyer.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
    Route::get('/marketplace', fn () => redirect()->route('consumer.marketplace'))->name('marketplace');
    Route::get('/marketplace/farmer/{farmer}', fn (App\Models\User $farmer) => redirect()->route('consumer.marketplace.farmer', $farmer))->name('marketplace.farmer');
    Route::get('/cart', fn () => redirect()->route('cart.index'))->name('cart');
    Route::get('/orders', fn () => redirect()->route('orders.index'))->name('orders');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancelByConsumer'])->name('orders.cancel');
    Route::get('/feedback', fn () => redirect()->route('consumer.feedback'))->name('feedback');
    Route::get('/{path}', fn () => redirect()->route('dashboard'))->where('path', '.*')->name('fallback');
});

// Farmer routes
Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
    Route::get('/decision-support', [DashboardController::class, 'farmerDecisionSupport'])->name('decision-support');
    Route::get('/sales-summary', [DashboardController::class, 'farmerSalesSummary'])->name('sales-summary');
    Route::get('/sales-summary/print', [DashboardController::class, 'printFarmerSalesSummary'])->name('sales-summary.print');
    Route::get('/inventory', [ProductController::class, 'inventory'])->name('inventory.index');
    Route::get('/inventory/print', [ProductController::class, 'printInventory'])->name('inventory.print');
    Route::patch('/inventory/{product}', [ProductController::class, 'updateInventory'])->name('inventory.update');
    Route::get('/products', [ProductController::class, 'farmerProducts'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::patch('/products/{product}/restock', [ProductController::class, 'restock'])->name('products.restock');
    Route::patch('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/orders/{order}/accept', [OrderController::class, 'acceptOrder'])->name('orders.accept');
    Route::patch('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::patch('/orders/{order}/reject-payment', [OrderController::class, 'rejectPayment'])->name('orders.reject-payment');
    Route::patch('/orders/{order}/preparing', [OrderController::class, 'markPreparing'])->name('orders.preparing');
    Route::patch('/orders/{order}/out-for-delivery', [OrderController::class, 'markOutForDelivery'])->name('orders.out-for-delivery');
    Route::patch('/orders/{order}/ready-for-pickup', [OrderController::class, 'markReadyForPickup'])->name('orders.ready-for-pickup');
    Route::patch('/orders/{order}/complete', [OrderController::class, 'markCompleted'])->name('orders.complete');
    Route::patch('/orders/{order}/complete-with-proof', [OrderController::class, 'completeWithProof'])->name('orders.complete-with-proof');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancelByFarmer'])->name('orders.cancel');
    Route::get('/{path}', fn () => redirect()->route('dashboard'))->where('path', '.*')->name('fallback');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('dashboard'))->name('dashboard');
    Route::get('/orders', fn () => redirect()->route('orders.index'))->name('orders');
    Route::get('/system-report/print', [AdminController::class, 'printSystemReport'])->name('system-report.print');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products-report/print', [AdminController::class, 'printProductsReport'])->name('products.print');
    Route::get('/products/{product}', [AdminController::class, 'showProduct'])->name('products.show');
    Route::get('/orders-report/print', [AdminController::class, 'printOrdersReport'])->name('orders.print');
    Route::get('/user-reports', [AdminController::class, 'userReports'])->name('user-reports');
    Route::get('/announcements', [AdminAnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AdminAnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [AdminAnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AdminAnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    Route::patch('/orders/{order}/payment-status', [AdminController::class, 'updatePaymentStatus'])->name('orders.payment-status');
    Route::patch('/orders/{order}/payment/paid', [AdminController::class, 'markPaymentPaid'])->name('orders.payment.paid');
    Route::patch('/orders/{order}/payment/reject', [AdminController::class, 'rejectPayment'])->name('orders.payment.reject');
    Route::patch('/orders/{order}/refund', [AdminController::class, 'markRefunded'])->name('orders.refund');
    Route::patch('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::patch('/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
    Route::patch('/feedback/{feedback}/read', [AdminController::class, 'markFeedbackRead'])->name('feedback.read');
    Route::patch('/feedback/{feedback}/resolve', [AdminController::class, 'resolveFeedback'])->name('feedback.resolve');
    Route::delete('/feedback/{feedback}', [AdminController::class, 'destroyFeedback'])->name('feedback.destroy');
    Route::get('/{path}', fn () => redirect()->route('dashboard'))->where('path', '.*')->name('fallback');
});
