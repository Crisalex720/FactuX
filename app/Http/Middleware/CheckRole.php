<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::guard('trabajador')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('trabajador')->user();
        $userRole = strtolower($user->cargo);

        // El usuario maestro tiene acceso a todo
        if ($userRole === 'maestro' || $userRole === 'master' || $userRole === 'admin') {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles requeridos
        foreach ($roles as $role) {
            if ($userRole === strtolower($role)) {
                return $next($request);
            }
        }

        // Si no tiene permisos, redirigir con mensaje de error
        return redirect()->route('inventario.index')->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}
