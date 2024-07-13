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


//  -------------------------- Profile Management Section ---------------------------------------------
// ---------------------------User Module------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// ---------------------------Vendor Module------------------------------
Route::middleware('vendor')->prefix('vendor.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'vendorEdit'])->name('vendor.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'vendorUpdate'])->name('vendor.profile.update');
    Route::delete('/profile', [ProfileController::class, 'vendorDestroy'])->name('vendor.profile.destroy');
    require __DIR__ . '/vendorAuth.php';
});
//  -------------------------- /Profile Management Section ---------------------------------------------
require __DIR__ . '/auth.php';



// ---------------------------- Dashboard Section ------------------------------------------------------
// -------------------------User Module-----------------------------
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/', UserHomeController::class)->middleware(['auth', 'verified'])->name('index');
});
// -------------------------Admin Module-----------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminHomeController::class)->middleware('admin')->name('index');
    require __DIR__ . '/adminAuth.php';
});
// -------------------------Vendor Module-----------------------------
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/', VendorHomeController::class)->middleware('vendor')->name('index');
    require __DIR__ . '/vendorAuth.php';
});
// ---------------------------- /Dashboard Section ----------------------------------------------------

// ----------------------------  Functionality Management ----------------------------------------------------
// -------------------------Admin Module-----------------------------
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/admin/users', [AdminController::class, 'indexUsers'])->name('admin.users.index');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    Route::get('/admin/vendors', [AdminController::class, 'indexVendors'])->name('admin.vendors.index');
    Route::delete('/admin/vendors/{id}', [AdminController::class, 'deleteVendors'])->name('admin.vendors.delete');

    Route::get('/products', [AdminController::class, 'indexProducts'])->name('admin.products.index');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');

    Route::get('/categories', [AdminController::class, 'indexCategories'])->name('admin.categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategories'])->name('admin.categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategories'])->name('admin.categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategories'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategories'])->name('admin.categories.delete');

    Route::get('/orders', [AdminController::class, 'indexOrders'])->name('admin.orders.index');
});

//  -------------------------------Vendor Module
Route::middleware('vendor')->prefix('vendor')->group(function () {
    // -------------------------------Product Management
    Route::get('/products', [VendorController::class, 'indexProducts'])->name('vendor.products.index');
    Route::get('/products/create', [VendorController::class, 'createProduct'])->name('vendor.products.create');
    Route::post('/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::put('/products/{id}', [VendorController::class, 'updateProduct'])->name('vendor.products.update');
    Route::delete('/products/{id}', [VendorController::class, 'deleteProduct'])->name('vendor.products.delete');
    // -------------------------------Order Management
    Route::get('/orders', [VendorController::class, 'indexOrders'])->name('vendor.orders.index');
    Route::get('/orders/show/{id}', [VendorController::class, 'viewOrder'])->name('vendor.orders.show');
    Route::patch('/orders/show/{id}', [VendorController::class, 'updateOrder'])->name('vendor.orders.update');
});

//  -------------------------------User Module
Route::middleware('auth')->prefix('user')->group(function () {
    // ----------------------------------Product Management
    Route::get('/products', [UserController::class, 'indexProducts'])->name('user.products.index');
    Route::get('/products/{id}', [UserController::class, 'showProduct'])->name('user.products.show');
    Route::get('/products/filter',[UserController::class,'filterByCategory'])->name('user.products.categoryfilter');
    // ----------------------------------Cart Management 
    Route::post('/products', [UserController::class, 'addToCart'])->name('user.cart.add');
    Route::delete('/cart/remove/{rowId}', [UserController::class, 'removeFromCart'])->name('user.cart.remove');
    // ----------------------------------Order Management 
    Route::get('/orders/create', [UserController::class, 'createOrder'])->name('user.orders.create');
    Route::post('/orders', [UserController::class, 'storeOrder'])->name('user.orders.store');
    Route::get('/orders', [UserController::class, 'indexOrders'])->name('user.orders.index');
    Route::get('/orders/show/{id}', [UserController::class, 'viewOrder'])->name('user.orders.show');
    Route::patch('/orders/show/{id}', [VendorController::class, 'updateOrder'])->name('user.orders.update');
    // ----------------------------------Review Management 
    Route::post('/reviews', [UserController::class, 'storeReview'])->name('user.reviews.store');
    Route::patch('/reviews', [UserController::class, 'updateReview'])->name('user.reviews.update');
    Route::delete('/reviews', [UserController::class, 'deleteReview'])->name('user.reviews.delete');
});
// ----------------------------  /Functionality Management ----------------------------------------------------