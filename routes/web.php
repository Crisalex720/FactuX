<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ClienteController;
// use App\Http\Controllers\ProductoController; // No existe aún
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\FacturacionController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\AuthController;

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/profile', [AuthController::class, 'profile'])->name('profile')->middleware('auth:trabajador');
Route::post('/profile/update-photo', [AuthController::class, 'updatePhoto'])->name('profile.update-photo')->middleware('auth:trabajador');

Route::get('/', function () {
    return redirect()->route('inventario.index');
});

// Rutas protegidas por autenticación
Route::middleware('auth:trabajador')->group(function () {
    
    // Rutas para usuarios - Solo maestro, ceo, admin, administrativo
    Route::prefix('usuarios')->name('usuarios.')->middleware('role:maestro,ceo,admin,administrativo')->group(function () {
        Route::get('/', [UsuariosController::class, 'index'])->name('index');
        Route::get('/roles', [UsuariosController::class, 'roles'])->name('roles');
        Route::post('/', [UsuariosController::class, 'store'])->name('store');
        Route::put('/{id}', [UsuariosController::class, 'update'])->name('update');
        Route::delete('/{id}', [UsuariosController::class, 'destroy'])->name('destroy');
    });

    // Rutas de facturación - Todos excepto reportes únicamente
    Route::prefix('facturacion')->name('facturacion.')->middleware('role:maestro,ceo,admin,administrativo,cajero,vendedor')->group(function () {
        Route::get('/', [FacturacionController::class, 'index'])->name('index');
        Route::post('/agregar', [FacturacionController::class, 'agregarProducto'])->name('agregar');
        Route::post('/agregar-ajax', [FacturacionController::class, 'agregarProductoAjax'])->name('agregar.ajax');
        Route::get('/quitar/{id}', [FacturacionController::class, 'quitarProducto'])->name('quitar');
        Route::delete('/quitar-ajax/{id}', [FacturacionController::class, 'quitarProductoAjax'])->name('quitar.ajax');
        Route::get('/carrito', [FacturacionController::class, 'obtenerCarrito'])->name('carrito');
        Route::post('/finalizar', [FacturacionController::class, 'finalizarFactura'])->name('finalizar');
    });

    // Rutas para el listado de facturas - Todos excepto cajero básico
    Route::prefix('facturas')->name('facturas.')->middleware('role:maestro,ceo,admin,administrativo,vendedor')->group(function () {
        Route::get('/', [FacturaController::class, 'index'])->name('index');
        Route::get('/anular/{id}', [FacturaController::class, 'anular'])->name('anular');
    });

    // Rutas para clientes - Todos pueden gestionar clientes
    Route::resource('clientes', ClienteController::class)->middleware('role:maestro,ceo,admin,administrativo,cajero,vendedor');

    // Rutas para inventario - Todos excepto cajero básico
    Route::middleware('role:maestro,ceo,admin,administrativo,vendedor')->group(function () {
        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
        Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
        Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
        Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
        Route::post('/inventario/ajustar-stock', [InventarioController::class, 'ajustarStock'])->name('inventario.ajustar-stock');
        Route::get('inventario/stock/bajo', [InventarioController::class, 'stockBajo'])->name('inventario.stock-bajo');
    });

    // Rutas de reportes - Solo roles administrativos
    Route::prefix('reportes')->name('reportes.')->middleware('role:maestro,ceo,admin,administrativo')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportesController::class, 'index'])->name('index');
        Route::get('/kardex', [App\Http\Controllers\ReportesController::class, 'kardex'])->name('kardex');
    });

    // Rutas para productos - Solo roles administrativos
    // Route::resource('productos', ProductoController::class)->middleware('role:maestro,ceo,admin,administrativo'); // Controller no existe aún
});

