<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Factura;
use App\Models\ListaProd;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportesController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    public function kardex(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'producto_id' => 'nullable|exists:producto,id_producto'
        ]);

        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin)->endOfDay() : Carbon::now()->endOfDay();
        $productoId = $request->producto_id;

        // Obtener datos del kardex con filtros de fecha
        $kardexData = $this->obtenerDatosKardex($fechaInicio, $fechaFin, $productoId);

        if ($request->has('generar_pdf')) {
            $accion = $request->input('generar_pdf');
            return $this->generarKardexPDF($kardexData, $fechaInicio, $fechaFin, $productoId, $accion);
        }

        // Obtener lista de productos para el filtro
        $productos = Producto::orderBy('nombre_prod')->get();

        return view('reportes.kardex', compact('kardexData', 'productos', 'fechaInicio', 'fechaFin', 'productoId'));
    }

    private function obtenerDatosKardex($fechaInicio, $fechaFin, $productoId = null)
    {
        // Primero obtener todos los productos que necesitamos mostrar
        $productosQuery = Producto::select('id_producto', 'nombre_prod', 'precio_ventap', 'cantidad_prod');
        if ($productoId) {
            $productosQuery->where('id_producto', $productoId);
        }
        $productos = $productosQuery->get();

        // Obtener movimientos con filtros de fecha
        $query = DB::table('lista_prod as lp')
            ->join('factura as f', 'lp.id_fact', '=', 'f.id_fact')
            ->join('producto as p', 'lp.id_producto', '=', 'p.id_producto')
            ->join('cliente as c', 'f.cliente', '=', 'c.id_cliente')
            ->select(
                'p.id_producto',
                'p.nombre_prod',
                'p.precio_ventap',
                'p.cantidad_prod',
                'lp.cantidad',
                'f.num_fact',
                'f.prefijo_fact',
                'f.fecha_factura',
                'f.estado',
                'c.nombre_cl as cliente'
            )
            ->where('f.estado', 'activa')
            ->whereBetween('f.fecha_factura', [$fechaInicio, $fechaFin]);

        if ($productoId) {
            $query->where('p.id_producto', $productoId);
        }

        $movimientos = $query->orderBy('f.fecha_factura')
                           ->orderBy('p.nombre_prod')
                           ->get();

        // Inicializar kardex con todos los productos
        $kardex = [];
        $stocksIniciales = $this->obtenerStocksIniciales($fechaInicio, $productoId);

        foreach ($productos as $producto) {
            $kardex[$producto->id_producto] = [
                'producto' => $producto->nombre_prod,
                'precio_unitario' => $producto->precio_ventap,
                'stock_inicial' => $stocksIniciales[$producto->id_producto] ?? $producto->cantidad_prod,
                'movimientos' => [],
                'stock_final' => $stocksIniciales[$producto->id_producto] ?? $producto->cantidad_prod
            ];
        }

        // Agregar movimientos a los productos correspondientes
        foreach ($movimientos as $movimiento) {
            $idProd = $movimiento->id_producto;

            // Calcular nuevo stock (resta porque es venta)
            $kardex[$idProd]['stock_final'] -= $movimiento->cantidad;

            $kardex[$idProd]['movimientos'][] = [
                'fecha' => $movimiento->fecha_factura ? Carbon::parse($movimiento->fecha_factura)->format('d/m/Y') : date('d/m/Y'),
                'factura' => $movimiento->prefijo_fact . '-' . $movimiento->num_fact,
                'cliente' => $movimiento->cliente,
                'tipo' => 'SALIDA',
                'cantidad' => $movimiento->cantidad,
                'valor_unitario' => $movimiento->precio_ventap,
                'valor_total' => $movimiento->cantidad * $movimiento->precio_ventap,
                'saldo' => $kardex[$idProd]['stock_final']
            ];
        }

        return $kardex;
    }

    private function obtenerStocksIniciales($fechaInicio, $productoId = null)
    {
        $query = Producto::select('id_producto', 'cantidad_prod');
        
        if ($productoId) {
            $query->where('id_producto', $productoId);
        }

        $productos = $query->get();
        $stocks = [];

        foreach ($productos as $producto) {
            // Obtener cantidad vendida antes de la fecha inicio
            $vendidoAntes = DB::table('lista_prod as lp')
                ->join('factura as f', 'lp.id_fact', '=', 'f.id_fact')
                ->where('lp.id_producto', $producto->id_producto)
                ->where('f.estado', 'activa')
                ->where('f.fecha_factura', '<', $fechaInicio)
                ->sum('lp.cantidad');

            $stocks[$producto->id_producto] = $producto->cantidad_prod + $vendidoAntes;
        }

        return $stocks;
    }

    private function generarKardexPDF($kardexData, $fechaInicio, $fechaFin, $productoId = null, $accion = '1')
    {
        $titulo = $productoId ? 'Kardex de Producto' : 'Kardex General de Inventario';
        $nombreProducto = null;

        if ($productoId) {
            $producto = Producto::find($productoId);
            $nombreProducto = $producto ? $producto->nombre_prod : 'Producto no encontrado';
        }

        // Si no hay datos de kardex, incluir productos sin movimientos
        if (empty($kardexData)) {
            $kardexData = $this->obtenerProductosSinMovimientos($fechaInicio, $fechaFin, $productoId);
        }

        $pdf = PDF::loadView('reportes.kardex-pdf', compact(
            'kardexData', 
            'fechaInicio', 
            'fechaFin', 
            'titulo', 
            'nombreProducto'
        ));

        $pdf->setPaper('A4', 'landscape');
        
        $nombreArchivo = 'kardex_' . $fechaInicio->format('Y-m-d') . '_' . $fechaFin->format('Y-m-d') . '.pdf';
        
        // Si la acción es 'download', descargar; si no, mostrar en navegador
        if ($accion === 'download') {
            return $pdf->download($nombreArchivo);
        } else {
            return $pdf->stream($nombreArchivo);
        }
    }

    private function obtenerProductosSinMovimientos($fechaInicio, $fechaFin, $productoId = null)
    {
        $query = Producto::select('id_producto', 'nombre_prod', 'precio_ventap', 'cantidad_prod');
        
        if ($productoId) {
            $query->where('id_producto', $productoId);
        }

        $productos = $query->get();
        $kardex = [];

        foreach ($productos as $producto) {
            $kardex[$producto->id_producto] = [
                'producto' => $producto->nombre_prod,
                'precio_unitario' => $producto->precio_ventap,
                'stock_inicial' => $producto->cantidad_prod,
                'movimientos' => [],
                'stock_final' => $producto->cantidad_prod
            ];
        }

        return $kardex;
    }
}