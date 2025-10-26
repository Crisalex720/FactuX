<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trabajador;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Ciudad;
use App\Services\RolePermissionService;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Controller
{
    public function index()
    {
        $currentUser = Auth::guard('trabajador')->user();
        
        // Obtener usuarios con sus relaciones usando Eloquent
        $query = Trabajador::with(['pais', 'departamento', 'ciudad']);
        
        // Si el usuario actual NO es maestro, ocultar el usuario maestro del listado
        if ($currentUser->cargo !== 'maestro') {
            $query->where('cedula', '!=', '999999999'); // Excluir usuario maestro
        }
        
        $usuarios = $query->orderBy('id_trab', 'desc')->get();

        // Obtener datos para selects
        $paises = Pais::orderBy('nombre_pais')->get();
        $departamentos = Departamento::orderBy('nombre_depart')->get();
        $ciudades = Ciudad::orderBy('nombre_ciudad')->get();
        
        // Obtener roles que el usuario actual puede asignar
        $rolesDisponibles = RolePermissionService::getAssignableRoles($currentUser->cargo);

        return view('usuarios.index', compact('usuarios', 'paises', 'departamentos', 'ciudades', 'rolesDisponibles'));
    }

    public function roles()
    {
        $currentUser = Auth::guard('trabajador')->user();
        
        $query = Trabajador::with(['pais', 'departamento', 'ciudad']);
        
        // Si el usuario actual NO es maestro, ocultar el usuario maestro del listado
        if ($currentUser->cargo !== 'maestro') {
            $query->where('cedula', '!=', '999999999'); // Excluir usuario maestro
        }
        
        $trabajadores = $query->orderBy('cargo')
            ->orderBy('nombre')
            ->get();

        return view('usuarios.roles', compact('trabajadores'));
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

        // Protección adicional: no permitir crear usuarios maestro
        if (strtolower($request->cargo) === 'maestro') {
            return redirect()->route('usuarios.index')->with('error', 'No se puede asignar el rol de maestro por seguridad del sistema.');
        }

        try {
            $fotoNombre = null;
            
            // Manejar subida de foto
            if ($request->hasFile('foto_perfil')) {
                $foto = $request->file('foto_perfil');
                $fotoNombre = time() . '_' . $request->cedula . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('uploads/perfiles'), $fotoNombre);
            }
            
            Trabajador::create([
                'cedula' => $request->cedula,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'cargo' => $request->cargo,
                'contraseña' => $request->contrasena,
                'foto_perfil' => $fotoNombre,
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
        $currentUser = Auth::guard('trabajador')->user();

        // Proteger usuario maestro - solo él mismo puede modificarse
        if ($trabajador->cedula == '999999999' && $currentUser->cedula != '999999999') {
            return redirect()->route('usuarios.index')->with('error', 'No tienes permisos para modificar el usuario maestro.');
        }

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

        // Protección adicional: no permitir cambiar rol a maestro (excepto si ya es maestro)
        if (strtolower($request->cargo) === 'maestro' && $trabajador->cedula != '999999999') {
            return redirect()->route('usuarios.index')->with('error', 'No se puede asignar el rol de maestro por seguridad del sistema.');
        }

        try {
            $updateData = [
                'cedula' => $request->cedula,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'cargo' => $request->cargo,
                'contraseña' => $request->contrasena,
                'id_pais' => $request->id_pais,
                'id_depart' => $request->id_depart,
                'id_ciudad' => $request->id_ciudad
            ];
            
            // Manejar subida de nueva foto
            if ($request->hasFile('foto_perfil')) {
                // Eliminar foto anterior si existe
                if ($trabajador->foto_perfil && file_exists(public_path('uploads/perfiles/' . $trabajador->foto_perfil))) {
                    unlink(public_path('uploads/perfiles/' . $trabajador->foto_perfil));
                }
                
                $foto = $request->file('foto_perfil');
                $fotoNombre = time() . '_' . $request->cedula . '.' . $foto->getClientOriginalExtension();
                $foto->move(public_path('uploads/perfiles'), $fotoNombre);
                $updateData['foto_perfil'] = $fotoNombre;
            }
            
            $trabajador->update($updateData);

            return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $trabajador = Trabajador::findOrFail($id);
            $currentUser = Auth::guard('trabajador')->user();

            // Proteger usuario maestro - no puede ser eliminado nunca
            if ($trabajador->cedula == '999999999') {
                return redirect()->route('usuarios.index')->with('error', 'El usuario maestro no puede ser eliminado por seguridad del sistema.');
            }

            // Evitar que usuarios se eliminen a sí mismos
            if ($trabajador->id_trab == $currentUser->id_trab) {
                return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
            }

            $trabajador->delete();
            
            return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')->with('error', 'Error al eliminar usuario: ' . $e->getMessage());
        }
    }
}
