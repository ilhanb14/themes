<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AuthController; // Add this line
use App\Models\Product;
use App\Http\Middleware\EnsureUserIsVerified;
use App\Http\Middleware\IsVerified;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update'); // PUT for updates
});

Route::get('/testing', function () {
    $product = Product::create([
        'name' => 'New Product',
        'price' => 99.99
    ]);
    $product->categories()->attach([1, 2, 7]);
});


// Content routes
Route::get('/cities', [CityController::class, 'index'])->middleware(['auth', IsVerified::class]);

// Dashboard route

Route::get('/about', function () {
    return view('about.index');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/themedays', function () {
    return view('themedays.index');
});

Route::get('/contact', function () {
    return view('contact.index');
});


// Registration Routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register'); // Use the imported class
Route::post('/register', [AuthController::class, 'register']); // Use the imported class

// Newsletter
Route::post('/newsletter', function () {
    return "Subscribed successfully";
});