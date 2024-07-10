<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorHomeController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::prefix('user')->name('user.')->group(function () {
    Route::get('/', UserHomeController::class)->middleware(['auth', 'verified'])->name('index');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminHomeController::class)->middleware('admin')->name('index');
    require __DIR__ . '/adminAuth.php';
});
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/', VendorHomeController::class)->middleware('vendor')->name('index');
    require __DIR__ . '/vendorAuth.php';
});
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/admin/users', [AdminController::class, 'indexUsers'])->name('admin.users.index');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    Route::get('/products', [AdminController::class, 'indexProducts'])->name('admin.products.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');

    Route::get('/categories', [AdminController::class, 'indexCategories'])->name('admin.categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategories'])->name('admin.categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategories'])->name('admin.categories.store');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategories'])->name('admin.categories.delete');
});
Route::middleware('vendor')->prefix('vendor')->group(function () {
    Route::get('/products', [VendorController::class, 'indexProducts'])->name('vendor.products.index');
    Route::get('/products/create', [VendorController::class, 'createProduct'])->name('vendor.products.create');
    Route::post('/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::patch('/products/{id}', [VendorController::class, 'updateProduct'])->name('vendor.products.update');
    Route::delete('/products/{id}', [VendorController::class, 'deleteProduct'])->name('vendor.products.delete');
    Route::get('/orders', [VendorController::class, 'indexOrders'])->name('vendor.orders.index');
});
Route::middleware('auth')->prefix('user')->group(function () {
    Route::get('/products', [UserController::class, 'indexProducts'])->name('user.products.index');
    Route::get('/products/{id}', [UserController::class, 'showProduct'])->name('user.products.show');
    Route::post('/cart/add', [UserController::class, 'addToCart'])->name('user.cart.add');
    Route::get('/orders/create', [UserController::class, 'createOrder'])->name('user.orders.create');
    Route::post('/orders', [UserController::class, 'storeOrder'])->name('user.orders.store');
    Route::get('/orders', [UserController::class, 'indexOrders'])->name('user.orders.index');
    Route::post('/reviews', [UserController::class, 'storeReview'])->name('user.reviews.store');
    Route::delete('/cart/remove/{id}', [UserController::class, 'removeFromCart'])->name('user.cart.remove');
});
