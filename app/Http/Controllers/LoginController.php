<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller {

    public function index() {
        // Primero, muestra la vista de inicio de sesión
        return view('Login.index'); // ← CAMBIADO a 'Login.index' con L mayúscula
    }

    public function login(Request $request) {
        // URL base de la API
        $baseApiUrl = config('app.backend_api');

        // URL completa del endpoint de login
        $loginUrl = $baseApiUrl . '/api/login';

        // Haz la solicitud POST al endpoint de login
        $response = Http::withOptions(['verify' => false])->post($loginUrl, [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Verificar la respuesta de la API
        if ($response->successful()) {
            // Obtener los datos de la respuesta
            $responseData = $response->json();

            // Verificar si la respuesta contiene el token y la información del usuario
            if (isset($responseData['token']) && isset($responseData['user'])) {
                // Si la respuesta contiene los datos esperados, almacenar el token y el rol en la sesión
                $token = $responseData['token'];
                $user = $responseData['user'];
                $role = $user['role']; // Suponiendo que el rol está en el objeto user
                $email = $user['email']; // Suponiendo que el correo electrónico está en el objeto user
                $name = $user['name']; // Suponiendo que el nombre está en el objeto user
                $userId = $user['id'];

                // Almacena el token y el rol en la sesión
                $request->session()->put('token', $token);
                $request->session()->put('role', $role);
                $request->session()->put('name', $name);
                $request->session()->put('email', $email);
                $request->session()->put('user_id', $userId);

                // Verifica el rol del usuario y redirige según el rol
                if (in_array($role, [1, 2])) {
                    return redirect()->route('products.index');
                }

                // Redirige al usuario a la página de inicio
                return redirect()->route('start.index');
            } else {
                // Si la respuesta no contiene los datos esperados, muestra un mensaje de error
                return back()->with('error', 'La respuesta de la API es incorrecta. Inténtalo de nuevo.');
            }
        } else {
            // Si la solicitud no fue exitosa, muestra un mensaje de error
            return back()->with('error', 'Contraseña incorrecta o correo electrónico inválido. Inténtalo de nuevo.');
        }
    }

    public function logout(Request $request) {
        try {
            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/logout';
            
            // Obtener el token de la sesión antes de eliminarlo
            $token = $request->session()->get('token');
            
            // Si hay token, hacer logout en el backend
            if ($token) {
                Http::withToken($token)
                    ->withOptions(['verify' => false])
                    ->timeout(3) // Timeout corto para no bloquear
                    ->post($apiUrl);
            }
            
        } catch (\Exception $e) {
            // Ignorar errores del backend, continuar con logout local
            \Log::info('Logout del backend falló, continuando con logout local: ' . $e->getMessage());
        }
        
        // Siempre limpiar la sesión localmente
        $request->session()->flush();
        $request->session()->regenerate();
        
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}