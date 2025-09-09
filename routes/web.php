<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Nämä reitit ovat vain kehityksessä ja testauksessa.
| Palauttavat Blade-näkymiä testilomakkeille.
|
*/

// =======================================================
// Etusivu
// =======================================================
Route::get('/', function () {
    // Blade-tiedosto: resources/views/welcome.blade.php
    return view('welcome'); 
});

// =======================================================
// Testilomakkeet
// =======================================================

// =======================================================
// Testilomake: Orders
// =======================================================
Route::get('/test-orders', function () {
    // Blade-tiedosto: resources/views/test-orders.blade.php
    return view('test-orders'); 
})->name('test.orders');

// =======================================================
// Testilomake: Subscription
// =======================================================
Route::get('/test-subscription', function () {
    // Blade-tiedosto: resources/views/test-subscription.blade.php
    return view('test-subscription'); 
})->name('test.subscription');
