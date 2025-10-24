<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Factura;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('vista_listado_facturas');
        
        // Filtro por estado
        $estado = $request->get('estado', 'activa');
        if ($estado !== 'todas') {
            $query->where('estado', $estado);
        }
        
        // Filtro por búsqueda (prefijo, consecutivo o cliente)
        $busqueda = $request->get('busqueda');
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('prefijo', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('consecutivo', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('nombre_cliente', 'ILIKE', "%{$busqueda}%");
            });
        }
        
        // Determinar cantidad de registros a mostrar
        $perPage = $request->get('per_page', 20);
        if ($perPage === 'all') {
            $facturas = $query->orderBy('consecutivo', 'desc')->get();
            $paginatedFacturas = null;
        } else {
            // Usar paginación manual
            $total = $query->count();
            $page = $request->get('page', 1);
            $offset = ($page - 1) * $perPage;
            
            $facturas = $query->orderBy('consecutivo', 'desc')
                            ->offset($offset)
                            ->limit($perPage)
                            ->get();
            
            // Crear objeto de paginación manual
            $paginatedFacturas = new \Illuminate\Pagination\LengthAwarePaginator(
                $facturas,
                $total,
                $perPage,
                $page,
                [
                    'path' => $request->url(),
                    'pageName' => 'page',
                ]
            );
            $paginatedFacturas->appends($request->query());
        }
        
        // Obtener estadísticas globales (sin filtros)
        $estadisticas = DB::table('vista_listado_facturas')->selectRaw('
            COUNT(*) as total_facturas,
            COUNT(CASE WHEN estado = \'activa\' THEN 1 END) as facturas_activas,
            COUNT(CASE WHEN estado = \'anulado\' THEN 1 END) as facturas_anuladas,
            COALESCE(SUM(CASE WHEN estado = \'activa\' THEN total_factura ELSE 0 END), 0) as total_vendido
        ')->first();
        
        return view('facturas.index', compact('facturas', 'paginatedFacturas', 'estado', 'busqueda', 'perPage', 'estadisticas'));
    }

    public function anular($idFactura)
    {
        $factura = Factura::findOrFail($idFactura);
        $factura->update(['estado' => 'anulado']);
        
        return redirect()->route('facturas.index')->with('success', 'Factura anulada correctamente');
    }
}
