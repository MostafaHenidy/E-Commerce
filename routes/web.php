<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorHomeController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Auth;
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
    Route::get('/products/{id}', [AdminController::class, 'showProduct'])->name('admin.products.show');


    Route::get('/categories', [AdminController::class, 'indexCategories'])->name('admin.categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategories'])->name('admin.categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategories'])->name('admin.categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategories'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategories'])->name('admin.categories.delete');

    Route::get('/orders', [AdminController::class, 'indexOrders'])->name('admin.orders.index');
    Route::get('/orders/{id}', [AdminController::class, 'viewOrder'])->name('admin.orders.show');

    Route::get('/roles', [AdminController::class, 'indexRoles'])->name('admin.roles.index');
});

//  -------------------------------Vendor Module
Route::middleware('vendor')->prefix('vendor')->group(function () {

    Route::get('/products', [VendorController::class, 'indexProducts'])->name('vendor.products.index');
    Route::post('/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::get('/products/{id}', [VendorController::class, 'showProduct'])->name('vendor.products.show');
    Route::put('/products/{id}', [VendorController::class, 'updateProduct'])->name('vendor.products.update');
    Route::delete('/products/{id}', [VendorController::class, 'deleteProduct'])->name('vendor.products.delete');
    Route::post('/products/upload/{id}', [VendorController::class, 'uploadMultipleImages'])->name('vendor.products.multi');
    Route::delete('/products/upload/{id}', [VendorController::class, 'deleteMultipleImages'])->name('vendor.products.multidelete');
    Route::post('products/discount', [VendorController::class, 'setDiscount'])->name('vendor.products.discount');

    Route::get('/orders', [VendorController::class, 'indexOrders'])->name('vendor.orders.index');
    Route::get('/orders/show/{id}', [VendorController::class, 'viewOrder'])->name('vendor.orders.show');
    Route::patch('/orders/show/{id}', [VendorController::class, 'updateOrder'])->name('vendor.orders.update');

    Route::get('/notification/read',function(){
        Auth::guard('vendor')->user()->notifications->markAsRead();
    })->name('vendor.notifications.read');
    
    Route::get('/notification/clear',function(){
        Auth::guard('vendor')->user()->notifications()->delete();
    })->name('vendor.notifications.clear');

});


//  -------------------------------User Module
Route::middleware('auth')->prefix('user')->group(function () {
    // Product Management
    Route::get('/products', [UserController::class, 'indexProducts'])->name('user.products.index');
    Route::get('/products/filter', [UserController::class, 'filterByCategory'])->name('user.products.categoryfilter');
    Route::get('/products/search', [UserController::class, 'search'])->name('user.products.search');
    Route::get('/products/wishlist', [UserController::class, 'indexWishlist'])->name('user.products.wishlist');
    Route::post('/products/{id}/wishlist', [UserController::class, 'addToWishlist'])->name('user.wishlist.add');
    Route::delete('/products/{id}/wishlist', [UserController::class, 'removeFromWishlist'])->name('user.wishlist.remove');
    Route::get('/products/{id}', [UserController::class, 'showProduct'])->name('user.products.show');

    // Cart Management
    Route::post('/cart/add', [UserController::class, 'addToCart'])->name('user.cart.add');
    Route::delete('/cart/remove/{rowId}', [UserController::class, 'removeFromCart'])->name('user.cart.remove');
    // Order Management
    Route::get('/orders/create', [UserController::class, 'createOrder'])->name('user.orders.create');
    Route::post('/orders', [UserController::class, 'storeOrder'])->name('user.orders.store');
    Route::get('/orders', [UserController::class, 'indexOrders'])->name('user.orders.index');
    Route::get('/orders/show/{id}', [UserController::class, 'viewOrder'])->name('user.orders.show');
    Route::patch('/orders/show/{id}', [VendorController::class, 'updateOrder'])->name('user.orders.update');

    // Review Management
    Route::post('/reviews', [UserController::class, 'storeReview'])->name('user.reviews.store');
    Route::patch('/reviews', [UserController::class, 'updateReview'])->name('user.reviews.update');
    Route::delete('/reviews', [UserController::class, 'deleteReview'])->name('user.reviews.delete');
});


// ----------------------------  /Functionality Management ----------------------------------------------------