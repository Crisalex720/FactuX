<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trabajador;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Ciudad;

class UsuariosController extends Controller
{
    public function index()
    {
        // Obtener usuarios con sus relaciones usando Eloquent
        $usuarios = Trabajador::with(['pais', 'departamento', 'ciudad'])
            ->orderBy('id_trab', 'desc')
            ->get();

        // Obtener datos para selects
        $paises = Pais::orderBy('nombre_pais')->get();
        $departamentos = Departamento::orderBy('nombre_depart')->get();
        $ciudades = Ciudad::orderBy('nombre_ciudad')->get();

        return view('usuarios.index', compact('usuarios', 'paises', 'departamentos', 'ciudades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|numeric|unique:trabajadores,cedula',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'contrasena' => 'required|string|min:4',
            'id_pais' => 'required|exists:pais,id_pais',
            'id_depart' => 'required|exists:departamento,id_depart',
            'id_ciudad' => 'required|exists:ciudad,id_ciudad'
        ]);

        try {
            Trabajador::create([
                'cedula' => $request->cedula,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'cargo' => $request->cargo,
                'contraseña' => $request->contrasena,
                'id_pais' => $request->id_pais,
                'id_depart' => $request->id_depart,
                'id_ciudad' => $request->id_ciudad
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al registrar usuario: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $trabajador = Trabajador::findOrFail($id);

        $request->validate([
            'cedula' => 'required|numeric|unique:trabajadores,cedula,' . $id . ',id_trab',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'contrasena' => 'required|string|min:4',
            'id_pais' => 'required|exists:pais,id_pais',
            'id_depart' => 'required|exists:departamento,id_depart',
            'id_ciudad' => 'required|exists:ciudad,id_ciudad'
        ]);

        try {
            $trabajador->update([
                'cedula' => $request->cedula,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'cargo' => $request->cargo,
                'contraseña' => $request->contrasena,
                'id_pais' => $request->id_pais,
                'id_depart' => $request->id_depart,
                'id_ciudad' => $request->id_ciudad
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $trabajador = Trabajador::findOrFail($id);
            $trabajador->delete();
            
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }
}
