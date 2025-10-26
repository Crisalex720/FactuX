<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Caja;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CajaController extends Controller
{
    public function index()
    {
        $cajaAbierta = Caja::obtenerAbierta();
        $cajasCerradas = Caja::with(['trabajadorApertura', 'trabajadorCierre'])
            ->cerradas()
            ->orderBy('fecha_cierre', 'desc')
            ->limit(10)
            ->get();

        return view('caja.index', compact('cajaAbierta', 'cajasCerradas'));
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'dinero_base' => 'required|numeric|min:0',
            'tipo_cierre' => 'required|in:diario,personalizado',
            'fecha_apertura' => 'required_if:tipo_cierre,personalizado|nullable|date',
        ], [
            'dinero_base.required' => 'El dinero base es obligatorio.',
            'dinero_base.numeric' => 'El dinero base debe ser un número.',
            'dinero_base.min' => 'El dinero base no puede ser negativo.',
            'tipo_cierre.required' => 'Debe seleccionar el tipo de cierre.',
            'fecha_apertura.required_if' => 'La fecha de apertura es obligatoria para cierre personalizado.',
            'fecha_apertura.date' => 'La fecha de apertura debe ser una fecha válida.',
        ]);

        // Verificar que no hay una caja abierta
        if (Caja::hayAbierta()) {
            return redirect()->route('caja.index')->with('error', 'Ya hay una caja abierta. Debe cerrarla primero.');
        }

        $fechaApertura = $request->tipo_cierre === 'diario' 
            ? Carbon::today()->setTime(6, 0, 0) 
            : Carbon::parse($request->fecha_apertura);

        try {
            Caja::create([
                'dinero_base' => $request->dinero_base,
                'fecha_apertura' => $fechaApertura,
                'tipo_cierre' => $request->tipo_cierre,
                'estado' => 'abierta',
                'id_trab_apertura' => Auth::guard('trabajador')->user()->id_trab,
            ]);

            return redirect()->route('caja.index')->with('success', 'Caja abierta correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('caja.index')->with('error', 'Error al abrir la caja: ' . $e->getMessage());
        }
    }

    public function cerrar(Request $request, $id)
    {
        $caja = Caja::findOrFail($id);
        
        $validationRules = [
            'dinero_contado' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ];
        
        $validationMessages = [
            'dinero_contado.required' => 'El dinero contado es obligatorio.',
            'dinero_contado.numeric' => 'El dinero contado debe ser un número.',
            'dinero_contado.min' => 'El dinero contado no puede ser negativo.',
        ];
        
        // Solo validar fecha_cierre para tipo personalizado
        if ($caja->tipo_cierre === 'personalizado') {
            $validationRules['fecha_cierre'] = 'required|date|after:' . $caja->fecha_apertura->format('Y-m-d H:i:s');
            $validationMessages['fecha_cierre.required'] = 'La fecha de cierre es obligatoria para cierre personalizado.';
            $validationMessages['fecha_cierre.date'] = 'La fecha de cierre debe ser una fecha válida.';
            $validationMessages['fecha_cierre.after'] = 'La fecha de cierre debe ser posterior a la apertura.';
        }
        
        $request->validate($validationRules, $validationMessages);

        if ($caja->estado === 'cerrada') {
            return redirect()->route('caja.index')->with('error', 'Esta caja ya está cerrada.');
        }

        // Validar rango máximo de un mes para cierre personalizado
        if ($caja->tipo_cierre === 'personalizado' && $request->filled('fecha_cierre')) {
            $fechaCierre = Carbon::parse($request->fecha_cierre);
            $fechaApertura = Carbon::parse($caja->fecha_apertura);
            
            if ($fechaCierre->diffInDays($fechaApertura) > 31) {
                return redirect()->route('caja.index')->with('error', 'El rango máximo para cierre personalizado es de un mes.');
            }
        }

        $fechaCierre = $caja->tipo_cierre === 'diario' 
            ? Carbon::now()
            : Carbon::parse($request->fecha_cierre);

        try {
            // Calcular total de ventas del período
            $totalVentas = $caja->calcularTotalVentas();
            
            // Actualizar la caja
            $caja->update([
                'dinero_contado' => $request->dinero_contado,
                'total_ventas' => $totalVentas,
                'fecha_cierre' => $fechaCierre,
                'estado' => 'cerrada',
                'id_trab_cierre' => Auth::guard('trabajador')->user()->id_trab,
                'observaciones' => $request->observaciones,
            ]);

            // Calcular diferencia
            $caja->calcularDiferencia();
            $caja->save();

            return redirect()->route('caja.index')->with('success', 'Caja cerrada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('caja.index')->with('error', 'Error al cerrar la caja: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $caja = Caja::with(['trabajadorApertura', 'trabajadorCierre'])->findOrFail($id);
        $facturas = $caja->getFacturasDelPeriodo();

        return view('caja.show', compact('caja', 'facturas'));
    }

    public function destroy($id)
    {
        $currentUser = Auth::guard('trabajador')->user();
        
        // Solo admin o superior pueden eliminar
        if (!in_array($currentUser->cargo, ['maestro', 'ceo', 'admin'])) {
            return redirect()->route('caja.index')->with('error', 'No tienes permisos para eliminar cierres de caja.');
        }

        try {
            $caja = Caja::findOrFail($id);
            
            if ($caja->estado === 'abierta') {
                return redirect()->route('caja.index')->with('error', 'No se puede eliminar una caja abierta.');
            }

            $caja->delete();
            return redirect()->route('caja.index')->with('success', 'Cierre de caja eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('caja.index')->with('error', 'Error al eliminar el cierre: ' . $e->getMessage());
        }
    }

    public function reportePdf($id)
    {
        $caja = Caja::with(['trabajadorApertura', 'trabajadorCierre'])->findOrFail($id);
        
        if ($caja->estado === 'abierta') {
            return redirect()->route('caja.index')->with('error', 'No se puede generar reporte de una caja abierta.');
        }

        $facturas = $caja->getFacturasDelPeriodo();
        
        // Estadísticas adicionales
        $estadisticas = [
            'total_facturas' => $facturas->count(),
            'total_productos_vendidos' => DB::table('factura as f')
                ->join('lista_prod as lp', 'f.id_fact', '=', 'lp.id_fact')
                ->where('f.estado', 'activa')
                ->whereBetween('f.created_at', [$caja->fecha_apertura, $caja->fecha_cierre])
                ->sum('lp.cantidad'),
            'factura_mayor' => $facturas->max('total_factura'),
            'factura_menor' => $facturas->min('total_factura'),
            'promedio_venta' => $facturas->avg('total_factura'),
        ];

        $pdf = PDF::loadView('caja.reporte-pdf', compact('caja', 'facturas', 'estadisticas'));
        
        $nombreArchivo = 'cierre_caja_' . $caja->id_caja . '_' . Carbon::parse($caja->fecha_cierre)->format('Y-m-d') . '.pdf';
        
        return $pdf->download($nombreArchivo);
    }
}
