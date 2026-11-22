<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WhatsAppController;

// Public Routes
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/menu', [PublicController::class, 'menu'])->name('menu');
Route::get('/nosotros', [PublicController::class, 'about'])->name('about');
Route::get('/pedidos', [PublicController::class, 'book'])->name('pedidos');
Route::post('/pedidos', [PublicController::class, 'placeOrder'])->name('pedidos.store');
Route::get('/pedidos/exito/{order}', [PublicController::class, 'orderSuccess'])->name('pedidos.success');

// Order Tracking
Route::get('/seguimiento', [PublicController::class, 'trackOrderForm'])->name('seguimiento.form');
Route::get('/seguimiento/{order}', [PublicController::class, 'trackOrder'])->name('seguimiento.track');

// AJAX: comunas por región (público, usado en formulario de pedidos)
Route::get('/api/regiones/{region}/comunas', [PublicController::class, 'getCommunesByRegion'])
    ->whereNumber('region')
    ->name('api.regiones.comunas');

// WhatsApp notifications
Route::post('/pedidos/{order}/whatsapp/codigo', [WhatsAppController::class, 'sendTrackingCode'])
    ->middleware('auth')
    ->name('whatsapp.sendTrackingCode');
Route::post('/pedidos/{order}/whatsapp/estado', [WhatsAppController::class, 'sendStatusUpdate'])
    ->middleware('auth')
    ->name('whatsapp.sendStatusUpdate');

// Authentication Routes
Route::get('/ingresar', [AuthController::class, 'showLogin'])->name('login');
Route::post('/ingresar', [AuthController::class, 'login'])->name('login.post');
Route::post('/salir', [AuthController::class, 'logout'])->name('logout');

// Protected Intranet Routes
Route::middleware(['auth'])->prefix('intranet')->group(function () {
    
    // Shared Dashboard (accessible to all operators: admin, chef, waiter, delivery, accountant)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Order Operations & Kitchen Board (accessible to all, but actions restricted by role)
    Route::get('/pedidos', [AdminController::class, 'ordersList'])->name('admin.orders.list');
    Route::get('/cocina', [AdminController::class, 'kitchenDashboard'])->name('admin.kitchen.index');
    Route::post('/pedidos/{order}/estado', [AdminController::class, 'orderUpdateStatus'])->name('admin.orders.updateStatus');
    Route::get('/pedidos/{order}/documento', [AdminController::class, 'showSIIInvoice'])->name('admin.orders.documento');

    // Mesas & Garzones visual index
    Route::get('/mesas', [AdminController::class, 'tablesIndex'])->name('admin.tables.index');
    Route::get('/garzones', [AdminController::class, 'waitersIndex'])->name('admin.waiters.index');

    // Tareas de Limpieza (accessible to all, including completion)
    Route::get('/limpieza', [AdminController::class, 'cleaningTasksIndex'])->name('admin.cleaning.index');
    Route::post('/limpieza/{task}/completar', [AdminController::class, 'cleaningTasksComplete'])->name('admin.cleaning.complete');
    
    // In-person / cashier order creation (admin and waiter only)
    Route::middleware(['role:admin,waiter'])->group(function() {
        Route::get('/pedidos/crear', [AdminController::class, 'orderCreate'])->name('admin.orders.create');
        Route::post('/pedidos/crear', [AdminController::class, 'orderStoreInternal'])->name('admin.orders.storeInternal');
        Route::get('/pedidos/{order}/imprimir', [AdminController::class, 'orderPrint'])->name('admin.orders.print');
        Route::post('/pedidos/{order}/asignar', [AdminController::class, 'orderAssignEmployee'])->name('admin.orders.assign');

        // Reservas (Reservations)
        Route::get('/reservas', [AdminController::class, 'reservationsIndex'])->name('admin.reservations.index');
        Route::post('/reservas', [AdminController::class, 'reservationsStore'])->name('admin.reservations.store');
        Route::put('/reservas/{reservation}', [AdminController::class, 'reservationsUpdate'])->name('admin.reservations.update');
        Route::delete('/reservas/{reservation}', [AdminController::class, 'reservationsDestroy'])->name('admin.reservations.destroy');
        Route::post('/reservas/{reservation}/estado', [AdminController::class, 'reservationsUpdateStatus'])->name('admin.reservations.updateStatus');

        // Table status updates & waiter assignment
        Route::post('/mesas/{table}/estado', [AdminController::class, 'tablesUpdateStatus'])->name('admin.tables.updateStatus');
        Route::post('/mesas/{table}/garzon', [AdminController::class, 'tablesAssignWaiter'])->name('admin.tables.assignWaiter');
    });

    // Cash register control (admin and waiter only)
    Route::middleware(['role:admin,waiter'])->group(function() {
        Route::get('/finanzas/caja', [AdminController::class, 'cashRegister'])->name('admin.finances.caja');
        Route::post('/finanzas/caja/abrir', [AdminController::class, 'openCashRegister'])->name('admin.finances.caja.abrir');
        Route::post('/finanzas/caja/cerrar', [AdminController::class, 'closeCashRegister'])->name('admin.finances.caja.cerrar');
    });

    // Financial reporting and transactions (admin and accountant only)
    Route::middleware(['role:admin,accountant'])->group(function() {
        Route::get('/finanzas', [AdminController::class, 'financesIndex'])->name('admin.finances.index');
        Route::post('/finanzas/transaccion', [AdminController::class, 'storeFinanceTransaction'])->name('admin.finances.storeTransaction');
    });

    // Ingredients & Inventory (admin and chef only)
    Route::middleware(['role:admin,chef'])->group(function() {
        Route::get('/inventario', [AdminController::class, 'ingredientsIndex'])->name('admin.ingredients.index');
        Route::post('/inventario', [AdminController::class, 'ingredientsStore'])->name('admin.ingredients.store');
        Route::put('/inventario/{ingredient}', [AdminController::class, 'ingredientsUpdate'])->name('admin.ingredients.update');
        Route::delete('/inventario/{ingredient}', [AdminController::class, 'ingredientsDestroy'])->name('admin.ingredients.destroy');
    });

    // Suppliers management (admin, accountant, and chef only)
    Route::middleware(['role:admin,accountant,chef'])->group(function() {
        Route::get('/proveedores', [AdminController::class, 'suppliersIndex'])->name('admin.suppliers.index');
        Route::post('/proveedores', [AdminController::class, 'suppliersStore'])->name('admin.suppliers.store');
        Route::put('/proveedores/{supplier}', [AdminController::class, 'suppliersUpdate'])->name('admin.suppliers.update');
        Route::delete('/proveedores/{supplier}', [AdminController::class, 'suppliersDestroy'])->name('admin.suppliers.destroy');
    });

    // Admin-Only Panel: Products, Recipes, Categories, Users, CMS, and Logs
    Route::middleware(['role:admin'])->group(function () {
        
        // Products CRUD
        Route::get('/productos', [AdminController::class, 'productsIndex'])->name('admin.products.index');
        Route::post('/productos', [AdminController::class, 'productsStore'])->name('admin.products.store');
        Route::put('/productos/{product}', [AdminController::class, 'productsUpdate'])->name('admin.products.update');
        Route::delete('/productos/{product}', [AdminController::class, 'productsDestroy'])->name('admin.products.destroy');

        // Recipes CRUD (attaches ingredients to products)
        Route::get('/productos/{product}/receta', [AdminController::class, 'recipesIndex'])->name('admin.recipes.index');
        Route::post('/productos/{product}/receta', [AdminController::class, 'recipesStore'])->name('admin.recipes.store');
        Route::delete('/recetas/{recipe}', [AdminController::class, 'recipesDestroy'])->name('admin.recipes.destroy');

        // Categories CRUD
        Route::get('/categorias', [AdminController::class, 'categoriesIndex'])->name('admin.categories.index');
        Route::post('/categorias', [AdminController::class, 'categoriesStore'])->name('admin.categories.store');
        Route::put('/categorias/{category}', [AdminController::class, 'categoriesUpdate'])->name('admin.categories.update');
        Route::delete('/categorias/{category}', [AdminController::class, 'categoriesDestroy'])->name('admin.categories.destroy');

        // Users & Employees CRUD
        Route::get('/usuarios', [AdminController::class, 'usersIndex'])->name('admin.users.index');
        Route::post('/usuarios', [AdminController::class, 'usersStore'])->name('admin.users.store');
        Route::put('/usuarios/{user}', [AdminController::class, 'usersUpdate'])->name('admin.users.update');
        Route::delete('/usuarios/{user}', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');

        // CMS Content Editor
        Route::get('/cms', [AdminController::class, 'cmsIndex'])->name('admin.cms.index');
        Route::post('/cms', [AdminController::class, 'cmsUpdate'])->name('admin.cms.update');

        // Audit Logs / Activity logs
        Route::get('/auditoria', [AdminController::class, 'activityLogs'])->name('admin.audit');

        // Tables CRUD (admin only)
        Route::post('/mesas', [AdminController::class, 'tablesStore'])->name('admin.tables.store');
        Route::put('/mesas/{table}', [AdminController::class, 'tablesUpdate'])->name('admin.tables.update');
        Route::delete('/mesas/{table}', [AdminController::class, 'tablesDestroy'])->name('admin.tables.destroy');

        // Waiters CRUD (admin only)
        Route::post('/garzones', [AdminController::class, 'waitersStore'])->name('admin.waiters.store');
        Route::put('/garzones/{waiter}', [AdminController::class, 'waitersUpdate'])->name('admin.waiters.update');
        Route::delete('/garzones/{waiter}', [AdminController::class, 'waitersDestroy'])->name('admin.waiters.destroy');

        // Cleaning Tasks CRUD (admin only)
        Route::post('/limpieza', [AdminController::class, 'cleaningTasksStore'])->name('admin.cleaning.store');
        Route::put('/limpieza/{task}', [AdminController::class, 'cleaningTasksUpdate'])->name('admin.cleaning.update');
        Route::delete('/limpieza/{task}', [AdminController::class, 'cleaningTasksDestroy'])->name('admin.cleaning.destroy');
    });
});
