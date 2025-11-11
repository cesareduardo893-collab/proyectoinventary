<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // TEMPORAL: Permitir acceso a todos los usuarios
        // Esto ayudará a identificar si el problema es de roles o de otra cosa
        return $next($request);
        
        /* COMENTADO TEMPORALMENTE
        $userRole = session('role');
        
        // Si no hay usuario autenticado, redirigir al login
        if (!$userRole) {
            return redirect('/login')->with('error', 'Por favor inicia sesión.');
        }

        // Verificar si el rol del usuario está en los roles permitidos
        if (!in_array($userRole, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
        */
    }
}