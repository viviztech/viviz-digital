<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Marketplace Home
Volt::route('/', 'marketplace.home');

// Customer Dashboard
// Customer Dashboard
// Customer Dashboard
Volt::route('/my-dashboard', 'customer.dashboard')->middleware(['auth', 'verified'])->name('customer.dashboard');
Volt::route('/my-library', 'customer.library')->middleware(['auth', 'verified'])->name('customer.library');
Volt::route('/my-wishlist', 'customer.wishlist')->middleware(['auth', 'verified'])->name('customer.wishlist');


// Auth
Volt::route('/login', 'auth.login')->name('login');
Volt::route('/register', 'auth.register')->name('register');
Volt::route('/forgot-password', 'auth.forgot-password')->name('password.request');
Volt::route('/password/reset/{token}', 'auth.reset-password')->name('password.reset');

// Logout Route (POST request usually preferred but for simple link access GET is easier if protected)
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Guest Route Group (Optional but good for organization)
Route::middleware('guest')->group(function () {
    // Other guest routes if needed
});

// Password Reset Handling (POST request handled by Livewire component but route needs name for redirect)
Route::post('/password/reset', function () {
    // This route is mainly for the 'password.update' name requirement by standard Laravel auth components
    // Actual logic is inside the Livewire component
})->name('password.update');

// Email Verification Routes
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::middleware('auth')->group(function () {
    Volt::route('/email/verify', 'auth.verify-email')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/my-library');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');
});


// Static Pages (Coming Soon)
// Category Routes (Redirect to Home with Filter)
foreach (['photos', 'videos', 'templates', 'audio', 'graphic'] as $type) {
    Route::get('/' . $type, fn() => redirect('/?type=' . Str::singular($type)));
}

// Static Pages
Route::view('/about', 'pages.about')->name('pages.about');
Route::view('/contact', 'pages.contact')->name('pages.contact');
Route::view('/blog', 'pages.blog')->name('pages.blog');

// Creator Pages
Route::view('/become-creator', 'pages.become-creator')->name('pages.become-creator');
Route::view('/creator-resources', 'pages.creator-resources')->name('pages.creator-resources');
Route::view('/payouts', 'pages.payouts')->name('pages.payouts');

// Legal Pages
Route::view('/terms', 'pages.legal.terms')->name('pages.terms');
Route::view('/privacy', 'pages.legal.privacy')->name('pages.privacy');
Route::view('/licenses', 'pages.legal.licenses')->name('pages.licenses');

// Product routes
// Product routes
Route::get('/products/{product:slug}', function (\App\Models\Product $product) {
    $categories = \App\Models\Product::where('is_active', true)->select('type')->distinct()->pluck('type');
    $recentProducts = \App\Models\Product::where('is_active', true)
        ->where('id', '!=', $product->id)
        ->latest()
        ->take(5)
        ->with('shop')
        ->get();

    return view('products.show', compact('product', 'categories', 'recentProducts'));
})->name('products.show');

// Payment Routes
Route::get('/checkout/{product:slug}', [\App\Http\Controllers\PaymentController::class, 'initiate'])->name('payment.initiate');
Route::post('/payment/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/success/{order}', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [\App\Http\Controllers\PaymentController::class, 'failed'])->name('payment.failed');

// Invoice Download
Route::get('/orders/{order}/invoice', [\App\Http\Controllers\OrderController::class, 'downloadInvoice'])
    ->middleware(['auth', 'verified'])
    ->name('orders.invoice');

// Secure download route
Route::get('/download/{token}', function (string $token) {
    $downloadToken = \App\Models\DownloadToken::where('token', $token)->firstOrFail();

    if (!$downloadToken->isValid()) {
        abort(403, 'Download link has expired or reached maximum downloads.');
    }

    $downloadToken->incrementDownloadCount();
    $product = $downloadToken->order->product;

    return response()->download(
        storage_path('app/' . $product->getDecryptedFilePath()),
        $product->name . '.' . pathinfo($product->getDecryptedFilePath(), PATHINFO_EXTENSION)
    );
})->name('download.file'); // Signed middleware removed for token-based access as implemented in logic
