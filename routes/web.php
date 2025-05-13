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


// API Documentation Routes
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index', [
        'documentationTitle' => 'docs',
        'documentation' => 'default',
        'urlsToDocs' => [url('/docs/api-docs.json')],
        'useAbsolutePath' => config('l5-swagger.defaults.paths.use_absolute_path', true),
        'operationsSorter' => config('l5-swagger.defaults.operations_sort', null),
        'configUrl' => config('l5-swagger.defaults.additional_config_url', null),
        'validatorUrl' => config('l5-swagger.defaults.validator_url', null),
    ]);
})->name('l5-swagger.default.api');

// Serve API Docs JSON file
Route::get('/docs/api-docs.json', function () {
    $filePath = storage_path('api-docs/api-docs.json');
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    return response()->json(['error' => 'API documentation not found'], 404);
});