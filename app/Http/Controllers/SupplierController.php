<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\SuppliersExport;
use Illuminate\Support\Facades\Log;
use Exception;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        try {
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/suppliers';
            $searchQuery = $request->input('query');
            $tipoProveedor = $request->input('tipo_proveedor');
            $order = $request->input('order', 'recent');

            $token = $request->session()->get('token');

            $page = $request->input('page', 1);
            $perPage = 10;

            // Construir URL con parámetros
            $apiParams = [];
            if ($searchQuery) {
                $apiParams['search'] = $searchQuery;
            }
            if ($tipoProveedor) {
                $apiParams['tipo_proveedor'] = $tipoProveedor;
            }
            if ($order) {
                $apiParams['order'] = $order;
            }

            $apiUrl .= '?' . http_build_query($apiParams);

            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data)) {
                    $total = count($data);
                    $currentPage = $page;
                    $lastPage = ceil($total / $perPage);
                    
                    $offset = ($page - 1) * $perPage;
                    $suppliers = array_slice($data, $offset, $perPage);
                } else {
                    $suppliers = [];
                    $total = 0;
                    $currentPage = 1;
                    $lastPage = 1;
                }

                $tiposProveedor = [
                    'Nacional' => 'Nacional',
                    'Internacional' => 'Internacional',
                    'Servicios' => 'Servicios',
                    'Productos' => 'Productos',
                    'Gobierno' => 'Gobierno'
                ];

                // Manejar descargas
                if ($request->has('download')) {
                    $downloadType = $request->input('download');
                    if ($downloadType === 'pdf') {
                        return $this->generatePDF($request);
                    } elseif ($downloadType === 'excel') {
                        $filePath = storage_path('temp/Proveedores_' . date('Y-m-d') . '.xlsx');
                        $export = new SuppliersExport($suppliers);
                        $export->export($filePath);
                        return response()->download($filePath)->deleteFileAfterSend(true);
                    }
                }

                return view('suppliers.index', compact('suppliers', 'searchQuery', 'tipoProveedor', 'order', 'total', 'currentPage', 'lastPage', 'tiposProveedor'));
            }

            return redirect()->back()->with('error', 'Error al obtener los proveedores de la API');
            
        } catch (Exception $e) {
            Log::error('Error en SupplierController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno del sistema: ' . $e->getMessage());
        }
    }

    public function generatePDF(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $baseApiUrl = config('app.backend_api');

            $apiUrl = $baseApiUrl . '/api/suppliers';
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();

                if (is_array($data)) {
                    $suppliers = $data;
                } else {
                    $suppliers = [];
                }

                if (empty($suppliers)) {
                    return back()->with('error', 'No hay proveedores para mostrar.');
                }

                $pdf = Pdf::loadView('suppliers.pdf', compact('suppliers'));
                $pdf->setPaper('A4', 'landscape');
                return $pdf->download('proveedores.pdf');

            } else {
                return back()->with('error', 'Error al obtener los proveedores de la API');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $tiposProveedor = [
            'Nacional' => 'Nacional',
            'Internacional' => 'Internacional',
            'Servicios' => 'Servicios',
            'Productos' => 'Productos',
            'Gobierno' => 'Gobierno'
        ];

        return view('suppliers.create', compact('tiposProveedor'));
    }

    public function store(Request $request)
    {
        try {
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/suppliers';

            $token = $request->session()->get('token');

            // Preparar datos para la API
            $formParams = [
                ['name' => 'codigo_proveedor', 'contents' => $request->input('codigo_proveedor')],
                ['name' => 'nombre_razon_social', 'contents' => $request->input('nombre_razon_social')],
                ['name' => 'rfc_identificacion_fiscal', 'contents' => $request->input('rfc_identificacion_fiscal')],
                ['name' => 'tipo_proveedor', 'contents' => $request->input('tipo_proveedor')],
                ['name' => 'telefono_principal', 'contents' => $request->input('telefono_principal')],
                ['name' => 'correo_electronico', 'contents' => $request->input('correo_electronico')],
                ['name' => 'persona_contacto', 'contents' => $request->input('persona_contacto')],
                ['name' => 'telefono_secundario', 'contents' => $request->input('telefono_secundario')],
                ['name' => 'correo_secundario', 'contents' => $request->input('correo_secundario')],
                ['name' => 'informacion_comercial', 'contents' => $request->input('informacion_comercial')],
                ['name' => 'horarios_atencion', 'contents' => $request->input('horarios_atencion')],
                ['name' => 'cuenta_bancaria', 'contents' => $request->input('cuenta_bancaria')],
                ['name' => 'metodos_pago', 'contents' => $request->input('metodos_pago')],
                ['name' => 'plazos_credito', 'contents' => $request->input('plazos_credito')],
                ['name' => 'direccion', 'contents' => $request->input('direccion')],
            ];

            // Manejar archivos
            if ($request->hasFile('archivo_adjunto_csf')) {
                $formParams[] = [
                    'name' => 'archivo_adjunto_csf',
                    'contents' => fopen($request->file('archivo_adjunto_csf')->getPathname(), 'r'),
                    'filename' => $request->file('archivo_adjunto_csf')->getClientOriginalName()
                ];
            }

            if ($request->hasFile('opinion_cumplimiento')) {
                $formParams[] = [
                    'name' => 'opinion_cumplimiento',
                    'contents' => fopen($request->file('opinion_cumplimiento')->getPathname(), 'r'),
                    'filename' => $request->file('opinion_cumplimiento')->getClientOriginalName()
                ];
            }

            $client = new Client();

            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'multipart' => $formParams,
                'verify' => false,
            ]);

            if ($response->getStatusCode() == 201) {
                return redirect()->route('suppliers.index')->with('success', 'Proveedor creado exitosamente.');
            } else {
                return back()->withInput()->withErrors('Error al crear el proveedor. Por favor, inténtalo de nuevo más tarde.');
            }
        } catch (Exception $e) {
            Log::error('Error en SupplierController@store: ' . $e->getMessage());
            return back()->withInput()->withErrors('Error interno al crear proveedor: ' . $e->getMessage());
        }
    }

    public function show($id, Request $request)
    {
        try {
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/suppliers/' . $id;

            $token = $request->session()->get('token');

            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->successful()) {
                $supplier = $response->json();
                return view('suppliers.show', compact('supplier'));
            }

            return redirect()->back()->with('error', 'Error al obtener el proveedor de la API');
        } catch (Exception $e) {
            Log::error('Error en SupplierController@show: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno al cargar proveedor: ' . $e->getMessage());
        }
    }

    public function edit($id, Request $request)
    {
        try {
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/suppliers/' . $id;

            $token = $request->session()->get('token');

            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->successful()) {
                $supplier = $response->json();
                
                $tiposProveedor = [
                    'Nacional' => 'Nacional',
                    'Internacional' => 'Internacional',
                    'Servicios' => 'Servicios',
                    'Productos' => 'Productos',
                    'Gobierno' => 'Gobierno'
                ];

                return view('suppliers.edit', compact('supplier', 'tiposProveedor'));
            }

            return redirect()->back()->with('error', 'Error al obtener el proveedor de la API');
        } catch (Exception $e) {
            Log::error('Error en SupplierController@edit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno al cargar edición: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/suppliers/' . $id;

            $token = $request->session()->get('token');

            // Preparar datos para la API
            $formParams = [
                ['name' => '_method', 'contents' => 'PUT'],
                ['name' => 'codigo_proveedor', 'contents' => $request->input('codigo_proveedor')],
                ['name' => 'nombre_razon_social', 'contents' => $request->input('nombre_razon_social')],
                ['name' => 'rfc_identificacion_fiscal', 'contents' => $request->input('rfc_identificacion_fiscal')],
                ['name' => 'tipo_proveedor', 'contents' => $request->input('tipo_proveedor')],
                ['name' => 'telefono_principal', 'contents' => $request->input('telefono_principal')],
                ['name' => 'correo_electronico', 'contents' => $request->input('correo_electronico')],
                ['name' => 'persona_contacto', 'contents' => $request->input('persona_contacto')],
                ['name' => 'telefono_secundario', 'contents' => $request->input('telefono_secundario')],
                ['name' => 'correo_secundario', 'contents' => $request->input('correo_secundario')],
                ['name' => 'informacion_comercial', 'contents' => $request->input('informacion_comercial')],
                ['name' => 'horarios_atencion', 'contents' => $request->input('horarios_atencion')],
                ['name' => 'cuenta_bancaria', 'contents' => $request->input('cuenta_bancaria')],
                ['name' => 'metodos_pago', 'contents' => $request->input('metodos_pago')],
                ['name' => 'plazos_credito', 'contents' => $request->input('plazos_credito')],
                ['name' => 'direccion', 'contents' => $request->input('direccion')],
            ];

            // Manejar archivos
            if ($request->hasFile('archivo_adjunto_csf')) {
                $formParams[] = [
                    'name' => 'archivo_adjunto_csf',
                    'contents' => fopen($request->file('archivo_adjunto_csf')->getPathname(), 'r'),
                    'filename' => $request->file('archivo_adjunto_csf')->getClientOriginalName()
                ];
            }

            if ($request->hasFile('opinion_cumplimiento')) {
                $formParams[] = [
                    'name' => 'opinion_cumplimiento',
                    'contents' => fopen($request->file('opinion_cumplimiento')->getPathname(), 'r'),
                    'filename' => $request->file('opinion_cumplimiento')->getClientOriginalName()
                ];
            }

            $client = new Client();

            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'multipart' => $formParams,
                'verify' => false,
            ]);

            if ($response->getStatusCode() == 200) {
                return redirect()->route('suppliers.index')->with('success', 'Proveedor actualizado exitosamente.');
            } else {
                return back()->withInput()->withErrors('Error al actualizar el proveedor. Por favor, inténtalo de nuevo más tarde.');
            }
        } catch (Exception $e) {
            Log::error('Error en SupplierController@update: ' . $e->getMessage());
            return back()->withInput()->withErrors('Error interno al actualizar proveedor: ' . $e->getMessage());
        }
    }

    public function destroy($id, Request $request)
    {
        try {
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/suppliers/' . $id;

            $token = $request->session()->get('token');

            $response = Http::withToken($token)->withOptions(['verify' => false])->delete($apiUrl);

            if ($response->successful()) {
                return redirect()->route('suppliers.index')->with('success', 'Proveedor eliminado exitosamente.');
            } else {
                $errorMessage = $response->json()['message'] ?? 'Error al eliminar el proveedor. Por favor, inténtalo de nuevo más tarde.';
                return redirect()->route('suppliers.index')->with('error', $errorMessage);
            }
        } catch (Exception $e) {
            Log::error('Error en SupplierController@destroy: ' . $e->getMessage());
            return redirect()->route('suppliers.index')->with('error', 'Error interno al eliminar proveedor: ' . $e->getMessage());
        }
    }
}