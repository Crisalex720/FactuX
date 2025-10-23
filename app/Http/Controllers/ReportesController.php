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

        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : Carbon::now()->subMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : Carbon::now();
        $productoId = $request->producto_id;

        // Obtener datos del kardex con filtros de fecha
        $kardexData = $this->obtenerDatosKardex($fechaInicio, $fechaFin, $productoId);

        if ($request->has('generar_pdf')) {
            return $this->generarKardexPDF($kardexData, $fechaInicio, $fechaFin, $productoId);
        }

        // Obtener lista de productos para el filtro
        $productos = Producto::orderBy('nombre_prod')->get();

        return view('reportes.kardex', compact('kardexData', 'productos', 'fechaInicio', 'fechaFin', 'productoId'));
    }

    private function obtenerDatosKardex($fechaInicio, $fechaFin, $productoId = null)
    {
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

        // Agrupar por producto y calcular saldos
        $kardex = [];
        $stocksIniciales = $this->obtenerStocksIniciales($fechaInicio, $productoId);

        foreach ($movimientos as $movimiento) {
            $idProd = $movimiento->id_producto;
            
            if (!isset($kardex[$idProd])) {
                $kardex[$idProd] = [
                    'producto' => $movimiento->nombre_prod,
                    'precio_unitario' => $movimiento->precio_ventap,
                    'stock_inicial' => $stocksIniciales[$idProd] ?? $movimiento->cantidad_prod,
                    'movimientos' => [],
                    'stock_final' => $stocksIniciales[$idProd] ?? $movimiento->cantidad_prod
                ];
            }

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

    private function generarKardexPDF($kardexData, $fechaInicio, $fechaFin, $productoId = null)
    {
        $titulo = $productoId ? 'Kardex de Producto' : 'Kardex General de Inventario';
        $nombreProducto = null;

        if ($productoId) {
            $producto = Producto::find($productoId);
            $nombreProducto = $producto ? $producto->nombre_prod : 'Producto no encontrado';
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
        
        return $pdf->download($nombreArchivo);
    }
}