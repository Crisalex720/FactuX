<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Factura;

class FacturaController extends Controller
{
    public function index()
    {
        // Obtener facturas con productos
        $facturas = DB::table('factura as f')
            ->leftJoin('cliente as c', 'f.cliente', '=', 'c.id_cliente')
            ->leftJoin('trabajadores as t', 'f.id_trab', '=', 't.id_trab')
            ->select([
                'f.*',
                'c.nombre_cl as nombre_cliente',
                't.nombre as atendido_por',
                DB::raw('COALESCE(f.prefijo_fact, \'FACT\') as prefijo_fact')
            ])
            ->orderBy('f.num_fact', 'desc')
            ->get()
            ->map(function ($factura) {
                // Calcular total y productos
                $productos = DB::table('lista_prod as lp')
                    ->join('producto as p', 'lp.id_producto', '=', 'p.id_producto')
                    ->where('lp.id_fact', $factura->id_fact)
                    ->select('p.nombre_prod', 'lp.cantidad', 'p.precio_ventap')
                    ->get();
                
                $total = $productos->sum(function ($p) {
                    return $p->precio_ventap * $p->cantidad;
                });
                
                $productosDetalle = $productos->map(function ($p) {
                    return $p->nombre_prod . ' (' . $p->cantidad . ')';
                })->implode(', ');
                
                $factura->total_factura = $total;
                $factura->productos_detalle = $productosDetalle;
                
                return $factura;
            });
        
        return view('facturas.index', compact('facturas'));
    }

    public function anular($idFactura)
    {
        $factura = Factura::findOrFail($idFactura);
        $factura->update(['estado' => 'anulado']);
        
        return redirect()->route('facturas.index')->with('success', 'Factura anulada correctamente');
    }
}
