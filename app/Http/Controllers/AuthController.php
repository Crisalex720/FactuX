<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Trabajador;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar el login
    public function login(Request $request)
    {
        $request->validate([
            'cedula' => 'required|numeric',
            'contraseña' => 'required|string',
        ], [
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.numeric' => 'La cédula debe ser numérica.',
            'contraseña.required' => 'La contraseña es obligatoria.',
        ]);

        // Buscar el trabajador por cédula
        $trabajador = Trabajador::where('cedula', $request->cedula)->first();

        if ($trabajador && $this->verificarContraseña($request->contraseña, $trabajador->contraseña)) {
            // Autenticar al usuario
            Auth::guard('trabajador')->login($trabajador, $request->filled('remember'));
            
            // Regenerar la sesión para seguridad
            $request->session()->regenerate();

            return redirect()->intended(route('inventario.index'))->with('success', 'Bienvenido, ' . $trabajador->nombre . '!');
        }

        return back()->withErrors([
            'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('cedula');
    }

    // Verificar contraseña (puede ser hash o texto plano)
    private function verificarContraseña($inputPassword, $storedPassword)
    {
        // Primero intentar verificar como hash
        if (strlen($storedPassword) > 32 && Hash::check($inputPassword, $storedPassword)) {
            return true;
        }
        
        // Si no es hash o no coincide, comparar directamente (para compatibilidad)
        return $inputPassword === $storedPassword;
    }

    // Cerrar sesión
    public function logout(Request $request)
    {
        Auth::guard('trabajador')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }

    // Mostrar perfil del usuario
    public function profile()
    {
        $trabajador = Auth::guard('trabajador')->user();
        
        // Cargar relaciones necesarias
        $trabajador->load(['pais', 'departamento', 'ciudad', 'facturas']);
        
        // Calcular total facturado
        $totalFacturado = DB::table('factura as f')
            ->join('lista_prod as lp', 'f.id_fact', '=', 'lp.id_fact')
            ->join('producto as p', 'lp.id_producto', '=', 'p.id_producto')
            ->where('f.id_trab', $trabajador->id_trab)
            ->where('f.estado', 'activa')
            ->sum(DB::raw('lp.cantidad * p.precio_ventap'));
        
        return view('auth.profile', compact('trabajador', 'totalFacturado'));
    }
}
