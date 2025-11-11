<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LocationsExport;
use Illuminate\Support\Facades\Log;
use Exception;

class LocationController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.backend_api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/api/locations');
            
            if ($response->successful()) {
                $locations = $response->json();

                // Manejar descargas
                if ($request->has('download')) {
                    $downloadType = $request->input('download');
                    if ($downloadType === 'pdf') {
                        return $this->generatePDF($locations);
                    } elseif ($downloadType === 'excel') {
                        $filePath = storage_path('temp/Ubicaciones_' . date('Y-m-d') . '.xlsx');
                        $export = new LocationsExport($locations);
                        $export->export($filePath);
                        return response()->download($filePath)->deleteFileAfterSend(true);
                    }
                }

                return view('locations.index', compact('locations'));
            } else {
                return redirect()->back()->with('error', 'Error al obtener las ubicaciones');
            }
        } catch (Exception $e) {
            Log::error('Error en LocationController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            
            $response = Http::withToken($token)->post($this->apiBaseUrl . '/api/locations', $request->all());
            
            if ($response->successful()) {
                return redirect()->route('locations.index')->with('success', 'Ubicación creada exitosamente');
            } else {
                $error = $response->json()['error'] ?? 'Error al crear la ubicación';
                return redirect()->back()->with('error', $error)->withInput();
            }
        } catch (Exception $e) {
            Log::error('Error en LocationController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error de conexión: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $token = request()->session()->get('token');
            
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/api/locations/' . $id);
            
            if ($response->successful()) {
                $location = $response->json();
                return view('locations.show', compact('location'));
            } else {
                return redirect()->back()->with('error', 'Ubicación no encontrada');
            }
        } catch (Exception $e) {
            Log::error('Error en LocationController@show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $token = request()->session()->get('token');
            
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/api/locations/' . $id);
            
            if ($response->successful()) {
                $location = $response->json();
                return view('locations.edit', compact('location'));
            } else {
                return redirect()->back()->with('error', 'Ubicación no encontrada');
            }
        } catch (Exception $e) {
            Log::error('Error en LocationController@edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $token = $request->session()->get('token');
            
            $response = Http::withToken($token)->put($this->apiBaseUrl . '/api/locations/' . $id, $request->all());
            
            if ($response->successful()) {
                return redirect()->route('locations.index')->with('success', 'Ubicación actualizada exitosamente');
            } else {
                $error = $response->json()['error'] ?? 'Error al actualizar la ubicación';
                return redirect()->back()->with('error', $error)->withInput();
            }
        } catch (Exception $e) {
            Log::error('Error en LocationController@update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error de conexión: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $token = request()->session()->get('token');
            
            $response = Http::withToken($token)->delete($this->apiBaseUrl . '/api/locations/' . $id);
            
            if ($response->successful()) {
                return redirect()->route('locations.index')->with('success', 'Ubicación eliminada exitosamente');
            } else {
                return redirect()->back()->with('error', 'Error al eliminar la ubicación');
            }
        } catch (Exception $e) {
            Log::error('Error en LocationController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF
     */
    private function generatePDF($locations)
    {
        try {
            if (empty($locations)) {
                return back()->with('error', 'No hay ubicaciones para mostrar.');
            }

            $pdf = Pdf::loadView('locations.pdf', compact('locations'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download('ubicaciones_' . date('Y-m-d') . '.pdf');

        } catch (Exception $e) {
            Log::error('Error en LocationController@generatePDF: ' . $e->getMessage());
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
}