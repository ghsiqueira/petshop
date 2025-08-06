<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PetshopController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeManagementController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PetshopController as AdminPetshopController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/petshops', [PetshopController::class, 'index'])->name('petshops.index');
Route::get('/petshops/{petshop}', [PetshopController::class, 'show'])->name('petshops.show');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');

// Rotas do carrinho
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

// Autenticação
Auth::routes();

// Rotas para usuários autenticados
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Lista de desejos (wishlist) - NOVA SEÇÃO
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    
    // Rotas para clientes
    Route::middleware(['role:client'])->group(function () {
        // Pets
        Route::resource('pets', PetController::class);
        
        // Agendamentos
        Route::resource('appointments', AppointmentController::class);
        
        // Pedidos
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'store']);
        
        // Avaliações
        Route::post('/products/{product}/reviews', [ReviewController::class, 'storeProductReview'])->name('products.reviews.store');
        Route::post('/services/{service}/reviews', [ReviewController::class, 'storeServiceReview'])->name('services.reviews.store');
    });
    
    // Rotas para funcionários
    Route::middleware(['role:employee'])->group(function () {
        Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
        Route::get('/employee/appointments', [EmployeeController::class, 'appointments'])->name('employee.appointments');
        Route::put('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    });
    
    // Rotas para petshops
    Route::middleware(['role:petshop'])->group(function () {
        // Dashboard
        Route::get('/petshop/dashboard', [PetshopController::class, 'dashboard'])->name('petshop.dashboard');
        
        // Produtos
        Route::get('/petshop/products', [ProductController::class, 'index'])->name('petshop.products.index');
        Route::get('/petshop/products/create', [ProductController::class, 'create'])->name('petshop.products.create');
        Route::post('/petshop/products', [ProductController::class, 'store'])->name('petshop.products.store');
        Route::get('/petshop/products/{product}/edit', [ProductController::class, 'edit'])->name('petshop.products.edit');
        Route::put('/petshop/products/{product}', [ProductController::class, 'update'])->name('petshop.products.update');
        Route::delete('/petshop/products/{product}', [ProductController::class, 'destroy'])->name('petshop.products.destroy');
        
        // Serviços
        Route::get('/petshop/services', [ServiceController::class, 'index'])->name('petshop.services.index');
        Route::get('/petshop/services/create', [ServiceController::class, 'create'])->name('petshop.services.create');
        Route::post('/petshop/services', [ServiceController::class, 'store'])->name('petshop.services.store');
        Route::get('/petshop/services/{service}/edit', [ServiceController::class, 'edit'])->name('petshop.services.edit');
        Route::put('/petshop/services/{service}', [ServiceController::class, 'update'])->name('petshop.services.update');
        Route::delete('/petshop/services/{service}', [ServiceController::class, 'destroy'])->name('petshop.services.destroy');
        Route::get('/petshop/services/{service}', [ServiceController::class, 'show'])->name('petshop.services.show');
        Route::patch('/petshop/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('petshop.services.toggle-status');

        // Funcionários
        Route::resource('petshop/employees', EmployeeManagementController::class)->names('petshop.employees');

        // Pedidos
        Route::get('/petshop/orders', [PetshopController::class, 'orders'])->name('petshop.orders');
        
        // Agendamentos
        Route::get('/petshop/appointments', [PetshopController::class, 'appointments'])->name('petshop.appointments');
        
        // Atualizar informações do petshop
        Route::put('/petshop/update', [PetshopController::class, 'update'])->name('petshop.update');
    });
    
    // Rotas para admin
    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class);
            Route::resource('permissions', PermissionController::class);
            Route::resource('petshops', AdminPetshopController::class);
        });
    });
});

// APIs para buscar serviços e funcionários (para agendamentos)
Route::middleware('auth')->group(function () {
    Route::get('/api/petshops/{petshop}/services', function($petshopId) {
        $services = \App\Models\Service::where('petshop_id', $petshopId)
                                      ->where('is_active', true)
                                      ->select('id', 'name', 'price', 'duration')
                                      ->get();
        return response()->json($services);
    });
    
    Route::get('/api/petshops/{petshop}/employees', function($petshopId) {
        $employees = \App\Models\Employee::where('petshop_id', $petshopId)
                                        ->with('user:id,name')
                                        ->get()
                                        ->map(function($employee) {
                                            return [
                                                'id' => $employee->id,
                                                'name' => $employee->user->name
                                            ];
                                        });
        return response()->json($employees);
    });
});