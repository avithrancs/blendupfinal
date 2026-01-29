<?php

use App\Http\Controllers\HomeController;
use App\Livewire\DrinkCatalog;
use App\Livewire\Admin\DrinkManager;
use App\Livewire\Admin\OrderBoard;

Route::get('/', HomeController::class)->name('home');
Route::get('/menu', DrinkCatalog::class)->name('menu');
Route::get('/cart', \App\Livewire\User\Cart::class)->name('cart');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.index');
        }
        return view('dashboard');
    })->name('dashboard');

    Route::get('/checkout', \App\Livewire\User\Checkout::class)->name('checkout');
    Route::get('/checkout/success', [\App\Http\Controllers\StripeController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [\App\Http\Controllers\StripeController::class, 'cancel'])->name('checkout.cancel');
    Route::get('/orders/{order}', \App\Livewire\User\OrderDetails::class)->name('orders.show');
});

Route::middleware([
    'auth:sanctum',
    'verified',
    'admin',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('index');
    Route::get('/drinks', DrinkManager::class)->name('drinks');
    Route::get('/orders', OrderBoard::class)->name('orders');
});
