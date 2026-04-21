<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Routes that require authentication
// Users
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurant.index');
Route::get('/restaurant/show/{id}', [RestaurantController::class, 'show'])->name('restaurant.show');
Route::get('/menu_items', [MenuController::class, 'index'])->name('menu.items');
Route::get('/menu_items/show/{id}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/restaurants/menus/{id}', [MenuController::class, 'menus'])->name('menu.menus');

Route::get('orders', [OrderController::class, 'index']);

// Fetch a single order by ID - GET http://localhost:8000/api/orders/{id}
Route::get('orders/{id}', [OrderController::class, 'show']);

// Update order status - PUT http://localhost:8000/api/orders/{id}
Route::put('orders/{id}', [OrderController::class, 'update']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Current logged-in user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/users', [AuthController::class, 'dashboard'])->name('users');


    // Restaurant routes
    Route::post('/restaurant/create', [RestaurantController::class, 'create'])->name('restaurant.create');
    Route::post('/restaurant/edit/{id}', [RestaurantController::class, 'edit'])->name('restaurant.edit');
    // Route::delete('/restaurant/delete/{id}', [RestaurantController::class, 'destroy'])->name('restaurant.delete');

    // Menu routes
    Route::post('/menu_items/create', [MenuController::class, 'create'])->name('menu.create');
    Route::post('/menu_items/edit/{id}', [MenuController::class, 'edit'])->name('menu.edit');

    Route::post('/cart', [CartController::class, 'addToCart'])->name('cart.addToCart');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/count', [CartController::class, 'cartCount'])->name('cart.cartCount');
    
    
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    
    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/members/store', [MemberController::class, 'storeMember']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/category/{id}', [CategoryController::class, 'show']);
    Route::post('/category/create', [CategoryController::class, 'store']);

});

Route::get('/run-migrations', function () {
        try {
            // This runs 'php artisan migrate'
            Artisan::call('migrate', [
                '--force' => true, // Necessary if running in production
            ]);
            
            return response()->json([
                'message' => 'Migrations executed successfully!',
                'output' => Artisan::output()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Migration failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    });


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache is cleared!";
});

Route::get('/debug-env', function () {
    return [
        'cloudinary_url_exists' => !empty(env('CLOUDINARY_URL')),
        'app_debug' => config('app.debug'),
        'config_cached' => app()->configurationIsCached(),
    ];
});