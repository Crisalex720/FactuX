<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Factura;

class FacturaController extends Controller
{
    public function index()
    {
        // Usar la vista de la base de datos que ya incluye fechas
        $facturas = DB::table('vista_listado_facturas')
            ->orderBy('consecutivo', 'desc')
            ->get();
        
        return view('facturas.index', compact('facturas'));
    }

    public function anular($idFactura)
    {
        $factura = Factura::findOrFail($idFactura);
        $factura->update(['estado' => 'anulado']);
        
        return redirect()->route('facturas.index')->with('success', 'Factura anulada correctamente');
    }
}
