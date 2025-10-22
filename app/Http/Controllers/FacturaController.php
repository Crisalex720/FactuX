<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Factura; // Asumiendo que tienes esta tabla
use App\Models\Cliente;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with('cliente')->orderBy('created_at', 'desc')->paginate(15);
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('facturas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0'
        ]);

        $factura = Factura::create($request->all());
        
        return redirect()->route('facturas.show', $factura)
                        ->with('success', 'Factura creada exitosamente');
    }

    public function show($id)
    {
        $factura = Factura::with('cliente', 'detalles')->findOrFail($id);
        return view('facturas.show', compact('factura'));
    }

    public function edit($id)
    {
        $factura = Factura::findOrFail($id);
        $clientes = Cliente::all();
        return view('facturas.edit', compact('factura', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        $factura = Factura::findOrFail($id);
        
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'total' => 'required|numeric|min:0'
        ]);

        $factura->update($request->all());
        
        return redirect()->route('facturas.show', $factura)
                        ->with('success', 'Factura actualizada exitosamente');
    }

    public function destroy($id)
    {
        $factura = Factura::findOrFail($id);
        $factura->delete();
        
        return redirect()->route('facturas.index')
                        ->with('success', 'Factura eliminada exitosamente');
    }
}
