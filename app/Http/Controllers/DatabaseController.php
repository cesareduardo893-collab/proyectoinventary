<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function index(Request $request)
    {
        $baseApiUrl = config('app.backend_api');
        
        // Solo rol 0 (admin) puede acceder
        if (session('role') === '2' || session('role') === '1') {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta página');
        }

        // Para Sanctum, necesitas obtener el token de Sanctum específicamente
        $token = $request->session()->get('sanctum_token') ?? $request->session()->get('token');
        
        if (!$token) {
            // Si no hay token, redirigir al login
            return redirect()->route('login')->with('error', 'Sesión expirada. Por favor inicie sesión nuevamente.');
        }

        return view('database.index', [
            'apiBackup' => $baseApiUrl . '/api/export-database',
            'apiImport' => $baseApiUrl . '/api/import-database',
            'apiReset' => $baseApiUrl . '/api/reset-database',
            'token' => $token,
            'baseApiUrl' => $baseApiUrl
        ]);
    }
}