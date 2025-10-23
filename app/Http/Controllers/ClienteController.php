<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Ciudad;

class ClienteController extends Controller
{
    public function index()
    {
        // Obtener clientes con sus relaciones usando Eloquent
        $clientes = Cliente::with(['pais', 'departamento', 'ciudad'])
            ->orderBy('id_cliente', 'desc')
            ->get();

        // Obtener datos para selects
        $paises = Pais::orderBy('nombre_pais')->get();
        $departamentos = Departamento::orderBy('nombre_depart')->get();
        $ciudades = Ciudad::orderBy('nombre_ciudad')->get();

        return view('clientes.index', compact('clientes', 'paises', 'departamentos', 'ciudades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|numeric|unique:cliente,cedula',
            'nombre_cl' => 'required|string|max:255',
            'celular' => 'nullable|numeric',
            'correo' => 'nullable|email|max:255',
            'id_pais' => 'required|exists:pais,id_pais',
            'id_depart' => 'required|exists:departamento,id_depart',
            'id_ciudad' => 'required|exists:ciudad,id_ciudad'
        ]);

        try {
            Cliente::create([
                'cedula' => $request->cedula,
                'nombre_cl' => $request->nombre_cl,
                'celular' => $request->celular,
                'correo' => $request->correo,
                'id_pais' => $request->id_pais,
                'id_depart' => $request->id_depart,
                'id_ciudad' => $request->id_ciudad
            ]);

            return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error', 'Error al registrar cliente: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $request->validate([
            'cedula' => 'required|numeric|unique:cliente,cedula,' . $id . ',id_cliente',
            'nombre_cl' => 'required|string|max:255',
            'celular' => 'nullable|numeric',
            'correo' => 'nullable|email|max:255',
            'id_pais' => 'required|exists:pais,id_pais',
            'id_depart' => 'required|exists:departamento,id_depart',
            'id_ciudad' => 'required|exists:ciudad,id_ciudad'
        ]);

        try {
            $cliente->update([
                'cedula' => $request->cedula,
                'nombre_cl' => $request->nombre_cl,
                'celular' => $request->celular,
                'correo' => $request->correo,
                'id_pais' => $request->id_pais,
                'id_depart' => $request->id_depart,
                'id_ciudad' => $request->id_ciudad
            ]);

            return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error', 'Error al actualizar cliente: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            
            // Verificar si el cliente tiene facturas asociadas
            if ($cliente->facturas()->count() > 0) {
                return redirect()->route('clientes.index')->with('error', 'No se puede eliminar el cliente porque tiene facturas asociadas.');
            }
            
            $cliente->delete();
            
            return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('clientes.index')->with('error', 'Error al eliminar cliente: ' . $e->getMessage());
        }
    }
}