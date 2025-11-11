<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DatabaseController extends Controller
{
    public function index(Request $request)
    {
        // Verificar permisos
        if (session('role') === '2' || session('role') === '1') {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta página');
        }

        // URLs de la API
        $baseApiUrl = config('app.backend_api');
        $apiBackup = $baseApiUrl . '/api/export-database';
        $apiImport = $baseApiUrl . '/api/import-database';
        $apiReset = $baseApiUrl . '/api/reset-database';

        // Obtener el token de la sesión
        $token = $request->session()->get('token');

        // Información del usuario para logs (opcional)
        $userInfo = [
            'id' => session('user_id'),
            'name' => session('user_name'),
            'role' => session('role')
        ];

        return view('database.index', compact(
            'apiBackup', 
            'apiImport', 
            'apiReset', 
            'token',
            'userInfo',
            'baseApiUrl'
        ));
    }

    /**
     * Método adicional para manejar respuestas de la API
     * (Opcional - para futuras expansiones)
     */
    private function handleApiResponse($response)
    {
        if ($response->successful()) {
            return $response->json();
        }

        // Log del error
        \Log::error('API Error in DatabaseController', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return null;
    }

    /**
     * Método para verificar conectividad con la API
     * (Opcional - para diagnóstico)
     */
    public function checkApiConnection(Request $request)
    {
        try {
            $baseApiUrl = config('app.backend_api');
            $token = $request->session()->get('token');
            
            $response = Http::withToken($token)
                ->withOptions(['verify' => false])
                ->timeout(10)
                ->get($baseApiUrl . '/api/users');
                
            return response()->json([
                'connected' => $response->successful(),
                'status' => $response->status()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'connected' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}