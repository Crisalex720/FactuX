<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\FacturacionController;
use App\Http\Controllers\UsuariosController;

Route::get('/', function () {
    return redirect()->route('inventario.index');
});

// Rutas para facturas
Route::resource('facturas', FacturaController::class);

// Rutas para clientes
Route::resource('clientes', ClienteController::class);

// Rutas para productos
Route::resource('productos', ProductoController::class);

// Rutas para inventario
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');
Route::delete('/inventario/{id}', [InventarioController::class, 'destroy'])->name('inventario.destroy');
Route::post('/inventario/ajustar-stock', [InventarioController::class, 'ajustarStock'])->name('inventario.ajustar-stock');
Route::get('inventario/stock/bajo', [InventarioController::class, 'stockBajo'])->name('inventario.stock-bajo');

// Rutas de facturación
Route::prefix('facturacion')->name('facturacion.')->group(function () {
    Route::get('/', [FacturacionController::class, 'index'])->name('index');
    Route::post('/agregar', [FacturacionController::class, 'agregarProducto'])->name('agregar');
    Route::get('/quitar/{id}', [FacturacionController::class, 'quitarProducto'])->name('quitar');
    Route::post('/finalizar', [FacturacionController::class, 'finalizarFactura'])->name('finalizar');
    Route::get('/anular/{id}', [FacturacionController::class, 'anularFactura'])->name('anular');
});

// Rutas de usuarios
Route::prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/', [UsuariosController::class, 'index'])->name('index');
    Route::post('/', [UsuariosController::class, 'store'])->name('store');
    Route::put('/{id}', [UsuariosController::class, 'update'])->name('update');
    Route::delete('/{id}', [UsuariosController::class, 'destroy'])->name('destroy');
});

// Rutas adicionales si las necesitas
Route::get('/reportes', function () {
    return view('reportes.index');
})->name('reportes');

