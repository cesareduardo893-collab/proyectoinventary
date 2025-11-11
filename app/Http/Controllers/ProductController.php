<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ProductsExport;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductController extends Controller {

    public function index(Request $request)
    {
        try {
            // URL base de la API de productos
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/products';
            $searchQuery = $request->input('query');
        
            // Parámetros de paginación
            $page = $request->input('page', 1);
            $perPage = 100;
        
            // Define la URL de búsqueda en la API
            $apiSearchUrl = $baseApiUrl . '/api/search';
        
            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }
        
            // Si hay una consulta de búsqueda, agrega el parámetro de búsqueda a la URL de la API de búsqueda
            if ($searchQuery) {
                $apiSearchUrl .= '?search=' . urlencode($searchQuery);
                $response = Http::withToken($token)->get($apiSearchUrl);
            } else {
                $response = Http::withToken($token)->get($apiUrl);
            }
        
            // Verifica si la solicitud fue exitosa
            if ($response->successful()) {
                $data = $response->json();
        
                // Manejar diferentes estructuras de respuesta
                if (is_array($data) && array_key_exists('data', $data)) {
                    $products = $data['data'];
                    $total = $data['total'] ?? count($products);
                    $currentPage = $data['current_page'] ?? 1;
                    $lastPage = $data['last_page'] ?? 1;
                } else {
                    $products = $data;
                    $total = count($products);
                    $currentPage = $page;
                    $lastPage = ceil($total / $perPage);
                    
                    // Aplicar paginación manual si es necesario
                    if ($total > $perPage) {
                        $products = array_slice($products, ($page - 1) * $perPage, $perPage);
                    }
                }

                // Calcular la cantidad de productos prestados para cada producto
                $products = $this->calculateLoanedQuantities($products);
        
                if ($request->has('download')) {
                    $downloadType = $request->input('download');
                    if ($downloadType === 'pdf') {
                        return $this->generatePDF($request);
                    } elseif ($downloadType === 'excel') {
                        $filePath = storage_path('temp/Inventario_' . date('Y-m-d') . '.xlsx');
                        $export = new ProductsExport($products);
                        $export->export($filePath);
                        return response()->download($filePath)->deleteFileAfterSend(true);
                    }
                }
        
                return view('products.index', compact('products', 'searchQuery', 'total', 'currentPage', 'lastPage'));
            } else {
                $errorMessage = $response->json()['error'] ?? 'Error desconocido al obtener productos';
                return redirect()->back()->with('error', 'Error al obtener los productos: ' . $errorMessage);
            }
        } catch (Exception $e) {
            Log::error('Error en ProductController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno del sistema: ' . $e->getMessage());
        }
    }

    /**
     * Calcular la cantidad de productos prestados para cada producto
     */
    private function calculateLoanedQuantities($products)
    {
        try {
            foreach ($products as &$product) {
                // El backend ahora proporciona loans_sum_quantity, que es la SUMA de las cantidades prestadas
                $product['loaned_quantity'] = $product['loans_sum_quantity'] ?? 0;
                // Calcular cantidad disponible
                $product['available_quantity'] = max(0, ($product['quantity'] ?? 0) - $product['loaned_quantity']);
            }

            return $products;
        } catch (Exception $e) {
            Log::error('Error en calculateLoanedQuantities: ' . $e->getMessage());
            return $products;
        }
    }

    /**
     * Generar PDF de productos
     */
    public function generatePDF(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $baseApiUrl = config('app.backend_api');

            if (!$token) {
                return back()->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Obtener productos desde la API
            $response = Http::withToken($token)->get($baseApiUrl . '/api/products');

            if ($response->successful()) {
                $data = $response->json();
                
                // Manejar diferentes estructuras de respuesta de la API
                if (isset($data['data'])) {
                    $products = $data['data'];
                } elseif (is_array($data) && !empty($data) && isset($data[0]['id'])) {
                    $products = $data;
                } else {
                    $products = [];
                }

                // Calcular cantidades prestadas para el PDF también
                $products = $this->calculateLoanedQuantities($products);

                if (empty($products)) {
                    return back()->with('error', 'No se encontraron productos para generar el PDF.');
                }

                // Generar PDF
                $pdf = Pdf::loadView('products.pdf', [
                    'products' => $products,
                    'title' => 'Inventario General',
                    'generated_at' => now()->format('d/m/Y H:i:s')
                ]);

                $pdf->setPaper('A4', 'landscape');
                
                return $pdf->download('inventario-general-' . date('Y-m-d') . '.pdf');

            } else {
                $statusCode = $response->status();
                $errorMessage = $response->json()['error'] ?? 'Error desconocido en la API';
                
                return back()->with('error', 'Error al obtener productos: ' . $errorMessage . ' (Código: ' . $statusCode . ')');
            }

        } catch (Exception $e) {
            Log::error('Error en generatePDF: ' . $e->getMessage());
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    public function show($id, Request $request) {
        try {
            // URL base de la API
            $baseApiUrl = config('app.backend_api');

            // URL de la API para obtener un producto específico
            $productApiUrl = $baseApiUrl . '/api/products/' . $id;

            // URL de la API para obtener todos los proyectos
            $projectsApiUrl = $baseApiUrl . '/api/getprojects';

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Realiza una solicitud HTTP GET a la API para obtener los datos del producto
            $productResponse = Http::withToken($token)->get($productApiUrl);

            // Realiza una solicitud HTTP GET a la API para obtener los datos de los proyectos
            $projectsResponse = Http::withToken($token)->get($projectsApiUrl);

            if ($productResponse->successful() && $projectsResponse->successful()) {
                // Decodifica la respuesta JSON del producto en un array asociativo
                $product = $productResponse->json();
                $projects = $projectsResponse->json();

                // Calcular cantidad prestada para este producto
                $product = $this->calculateLoanedQuantities([$product])[0];

                // Muestra la vista de detalles del producto con los datos del producto y los proyectos
                return view('products.show', compact('product', 'projects'));
            } else {
                $error = $productResponse->successful() ? 'Error al obtener proyectos' : 'Error al obtener producto';
                return abort(404, $error);
            }

        } catch (Exception $e) {
            Log::error('Error en ProductController@show: ' . $e->getMessage());
            return abort(404, 'Error al cargar los datos del producto.');
        }
    }

    public function storeEntrance(Request $request) {
        try {
            // Validar los datos de la solicitud
            $validatedData = $request->validate([
                'project_id' => 'nullable|integer',
                'product_id' => 'required|integer',
                'user_id' => 'required|integer',
                'responsible' => 'required|string|max:100',
                'quantity' => 'required|integer|min:1',
                'description' => 'nullable|string|max:100',
                'folio' => 'nullable|string|max:100', 
            ]);

            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/entrances';

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Realizar una solicitud HTTP POST a tu segunda API con los datos validados del formulario
            $response = Http::withToken($token)->post($apiUrl, $validatedData);

            // Verificar si la solicitud fue exitosa
            if ($response->successful()) {
                return redirect()->route('products.index')->with('success', 'Entrada creada exitosamente.');
            } else {
                $error = $response->json()['error'] ?? 'Ocurrió un error desconocido al crear la entrada.';
                return redirect()->back()->withErrors(['quantity' => $error])->withInput();
            }

        } catch (Exception $e) {
            Log::error('Error en storeEntrance: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error interno al crear entrada: ' . $e->getMessage()])->withInput();
        }
    }

    public function storeOutputs(Request $request) {
        try {
            // Validar los datos de la solicitud
            $validatedData = $request->validate([
                'project_id' => 'nullable|integer',
                'product_id' => 'required|integer',
                'user_id' => 'required|integer',
                'responsible' => 'required|string|max:100',
                'quantity' => 'required|integer|min:1',
                'description' => 'nullable|string|max:100',
            ]);

            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/outputs';

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Realizar una solicitud HTTP POST a tu segunda API con los datos validados del formulario
            $response = Http::withToken($token)->post($apiUrl, $validatedData);

            // Verificar si la solicitud fue exitosa
            if ($response->successful()) {
                return redirect()->route('products.index')->with('success', 'Salida creada exitosamente.');
            } else {
                $error = $response->json()['error'] ?? 'Ocurrió un error desconocido al crear la salida.';
                return redirect()->back()->withErrors(['quantity' => $error])->withInput();
            }

        } catch (Exception $e) {
            Log::error('Error en storeOutputs: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error interno al crear salida: ' . $e->getMessage()])->withInput();
        }
    }

    public function storeLoans(Request $request) {
        try {
            // Validar los datos de la solicitud
            $validatedData = $request->validate([
                'product_id' => 'required|integer',
                'project_id' => 'nullable|integer',
                'user_id' => 'required|integer',
                'responsible' => 'required|string|max:100',
                'quantity' => 'required|integer|min:1',
                'observations' => 'nullable|string|max:255',
            ]);

            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/loans';

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Realizar una solicitud HTTP POST a tu segunda API con los datos validados del formulario
            $response = Http::withToken($token)->post($apiUrl, $validatedData);

            // Verificar si la solicitud fue exitosa
            if ($response->successful()) {
                return redirect()->route('products.index')->with('success', 'Préstamo creado exitosamente.');
            } else {
                $error = $response->json()['error'] ?? 'Ocurrió un error desconocido al crear el préstamo.';
                return redirect()->back()->withErrors(['quantity' => $error])->withInput();
            }

        } catch (Exception $e) {
            Log::error('Error en storeLoans: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Error interno al crear préstamo: ' . $e->getMessage()])->withInput();
        }
    }

    public function loansGet($id, Request $request) {
        try {
            // URL base de la API
            $baseApiUrl = config('app.backend_api');
        
            // URL de la API para obtener un producto específico
            $productApiUrl = $baseApiUrl . '/api/products/' . $id;
        
            // URL de la API para obtener todos los proyectos
            $projectsApiUrl = $baseApiUrl . '/api/getprojects';
        
            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }
        
            // Realiza una solicitud HTTP GET a la API para obtener los datos del producto
            $productResponse = Http::withToken($token)->get($productApiUrl);
        
            // Realiza una solicitud HTTP GET a la API para obtener los datos de los proyectos
            $projectsResponse = Http::withToken($token)->get($projectsApiUrl);
        
            if ($productResponse->successful() && $projectsResponse->successful()) {
                // Decodifica la respuesta JSON del producto en un array asociativo
                $product = $productResponse->json();
        
                // Decodifica la respuesta JSON de los proyectos en un array asociativo
                $projects = $projectsResponse->json();

                // Calcular cantidad prestada para este producto
                $product = $this->calculateLoanedQuantities([$product])[0];
        
                // Muestra la vista de detalles del producto con los datos del producto y los proyectos
                return view('products.loans', compact('product', 'projects'));
            }
        
            // En caso de error en alguna solicitud, redirige a una vista de error o de no encontrado
            return abort(404, 'Product or projects data not found.');
        
        } catch (Exception $e) {
            Log::error('Error en loansGet: ' . $e->getMessage());
            return abort(404, 'Error al cargar los datos para préstamo.');
        }
    }
    
    public function outPutGet($id, Request $request) {
        try {
            // URL base de la API
            $baseApiUrl = config('app.backend_api');

            // URL de la API para obtener un producto específico
            $productApiUrl = $baseApiUrl . '/api/products/' . $id;

            // URL de la API para obtener todos los proyectos
            $projectsApiUrl = $baseApiUrl . '/api/getprojects';

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Realiza una solicitud HTTP GET a la API para obtener los datos del producto
            $productResponse = Http::withToken($token)->get($productApiUrl);

            // Realiza una solicitud HTTP GET a la API para obtener los datos de los proyectos
            $projectsResponse = Http::withToken($token)->get($projectsApiUrl);

            if ($productResponse->successful() && $projectsResponse->successful()) {
                // Decodifica la respuesta JSON del producto en un array asociativo
                $product = $productResponse->json();

                // Decodifica la respuesta JSON de los proyectos en un array asociativo
                $projects = $projectsResponse->json();

                // Calcular cantidad prestada para este producto
                $product = $this->calculateLoanedQuantities([$product])[0];

                // Muestra la vista de detalles del producto con los datos del producto y los proyectos
                return view('products.output', compact('product', 'projects'));
            }

            // En caso de error en alguna solicitud, redirige a una vista de error o de no encontrado
            return abort(404, 'Product or projects data not found.');

        } catch (Exception $e) {
            Log::error('Error en outPutGet: ' . $e->getMessage());
            return abort(404, 'Error al cargar los datos para salida.');
        }
    }

    public function create(Request $request) {
        try {
            $baseApiUrl = config('app.backend_api');
            $token = $request->session()->get('token');

            // Verificar el rol del usuario desde la sesión
            $userRole = $request->session()->get('role');
            if ($userRole == 2) {
                return redirect()->back()->with('error', 'No estás autorizado para realizar esta acción.');
            }

            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // CORREGIDO: Usar el endpoint unificado que proporciona categorías, proveedores y ubicaciones
            $categoryProductsResponse = Http::withToken($token)->get($baseApiUrl . '/api/getCategoryProducts');

            $suppliers = [];
            $categories = [];
            $locations = [];

            if ($categoryProductsResponse->successful()) {
                $data = $categoryProductsResponse->json();
                
                // Extraer los datos de la respuesta unificada
                $categories = $data['categories'] ?? [];
                $suppliers = $data['suppliers'] ?? [];
                $locations = $data['locations'] ?? [];
                
                Log::info('Datos cargados exitosamente - Categorías: ' . count($categories) . ', Proveedores: ' . count($suppliers) . ', Ubicaciones: .' . count($locations));
            } else {
                Log::error('Error cargando datos del endpoint unificado: ' . $categoryProductsResponse->status());
                // Intentar cargar datos por separado como fallback
                $this->loadDataSeparately($token, $baseApiUrl, $suppliers, $categories, $locations);
            }

            return view('products.create', compact('suppliers', 'categories', 'locations'));

        } catch (Exception $e) {
            Log::error('Error en ProductController@create: ' . $e->getMessage());
            
            // En caso de error, usar arrays vacíos
            $suppliers = [];
            $categories = [];
            $locations = [];
            
            return view('products.create', compact('suppliers', 'categories', 'locations'))
                ->with('error', 'Error al cargar los datos. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Método auxiliar para cargar datos por separado como fallback
     */
    private function loadDataSeparately($token, $baseApiUrl, &$suppliers, &$categories, &$locations) {
        try {
            // Cargar categorías
            $categoriesResponse = Http::withToken($token)->get($baseApiUrl . '/api/categories');
            if ($categoriesResponse->successful()) {
                $categoriesData = $categoriesResponse->json();
                $categories = isset($categoriesData['data']) ? $categoriesData['data'] : $categoriesData;
            }

            // Cargar proveedores
            $suppliersResponse = Http::withToken($token)->get($baseApiUrl . '/api/suppliers');
            if ($suppliersResponse->successful()) {
                $suppliersData = $suppliersResponse->json();
                $suppliers = isset($suppliersData['data']) ? $suppliersData['data'] : $suppliersData;
            }

            // Cargar ubicaciones
            $locationsResponse = Http::withToken($token)->get($baseApiUrl . '/api/locations');
            if ($locationsResponse->successful()) {
                $locationsData = $locationsResponse->json();
                $locations = isset($locationsData['data']) ? $locationsData['data'] : $locationsData;
            }
        } catch (Exception $e) {
            Log::error('Error en loadDataSeparately: ' . $e->getMessage());
        }
    }

    public function store(Request $request) {
        try {
            // Validar los datos de la solicitud
            $validatedData = $request->validate([
                'name' => 'required|string|max:50',
                'model' => 'nullable|string|max:50',
                'measurement_unit' => 'nullable|string|max:15',
                'brand' => 'nullable|string|max:50',
                'quantity' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'price' => 'required|numeric|between:0,999999.99',
                'profile_image' => 'nullable|file|max:2048|mimes:jpeg,png,jpg,gif,svg',
                'serie' => 'nullable|string|max:40',
                'observations' => 'nullable|string|max:50',
                'location' => 'nullable|string|max:20',
                'location_id' => 'nullable|integer',
                'category_id' => 'required|integer',
                'supplier_id' => 'nullable|integer',
            ]);

            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/products';

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Verificar si la solicitud contiene una imagen
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $imageContents = file_get_contents($file->getPathname());
                $imageName = $file->getClientOriginalName();

                // Realizar una solicitud HTTP POST a tu API con los datos validados del formulario
                $response = Http::withToken($token)->attach(
                    'profile_image',
                    $imageContents,
                    $imageName
                )->post($apiUrl, $validatedData);
            } else {
                // Si no hay imagen adjunta, elimina el campo de imagen de los datos validados
                unset($validatedData['profile_image']);

                // Realizar una solicitud HTTP POST a tu API sin el campo de imagen
                $response = Http::withToken($token)->post($apiUrl, $validatedData);
            }

            // Verificar si la solicitud fue exitosa
            if ($response->successful()) {
                return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
            } else {
                $errorMessage = $response->json()['error'] ?? 'Error al crear el producto. Por favor, inténtalo de nuevo más tarde.';
                return back()->withInput()->withErrors(['error' => $errorMessage]);
            }

        } catch (Exception $e) {
            Log::error('Error en ProductController@store: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error interno al crear producto: ' . $e->getMessage()]);
        }
    }

    public function edit($id, Request $request) {
        try {
            // Verificar el rol del usuario desde la sesión
            $userRole = $request->session()->get('role');

            if ($userRole == 2) {
                return redirect()->back()->with('error', 'No estás autorizado para realizar esta acción.');
            }

            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // CORREGIDO: Usar el endpoint unificado que proporciona categorías, proveedores y ubicaciones
            $categoryProductsResponse = Http::withToken($token)->get($baseApiUrl . '/api/getCategoryProducts');

            $suppliers = [];
            $categories = [];
            $locations = [];

            if ($categoryProductsResponse->successful()) {
                $data = $categoryProductsResponse->json();
                
                // Extraer los datos de la respuesta unificada
                $categories = $data['categories'] ?? [];
                $suppliers = $data['suppliers'] ?? [];
                $locations = $data['locations'] ?? [];
            } else {
                // Intentar cargar datos por separado como fallback
                $this->loadDataSeparately($token, $baseApiUrl, $suppliers, $categories, $locations);
            }

            // URL de la API para obtener un producto específico
            $productApiUrl = $baseApiUrl . '/api/products/' . $id;

            // Realiza una solicitud HTTP GET a la API para obtener los datos del producto
            $productResponse = Http::withToken($token)->get($productApiUrl);

            // Verifica si la solicitud fue exitosa
            if ($productResponse->successful()) {
                // Decodifica la respuesta JSON en un array asociativo
                $product = $productResponse->json();

                // Calcular cantidad prestada para este producto
                $product = $this->calculateLoanedQuantities([$product])[0];

                // Muestra el formulario de edición con los datos del producto
                return view('products.edit', compact('product', 'suppliers', 'categories', 'locations'));
            } else {
                return back()->withErrors('Error al obtener los datos del producto. Por favor, inténtalo de nuevo más tarde.');
            }

        } catch (Exception $e) {
            Log::error('Error en ProductController@edit: ' . $e->getMessage());
            return back()->withErrors('Error interno al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id) {
        try {
            // Validar los datos de la solicitud
            $validatedData = $request->validate([
                'name' => 'required|string|max:50',
                'model' => 'nullable|string|max:50',
                'measurement_unit' => 'nullable|string|max:15',
                'brand' => 'nullable|string|max:50',
                'quantity' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'price' => 'required|numeric|between:0,999999.99',
                'profile_image' => 'nullable|file|max:2048|mimes:jpeg,png,jpg,gif,svg',
                'serie' => 'nullable|string|max:40',
                'observations' => 'nullable|string|max:50',
                'location' => 'nullable|string|max:100',
                'location_id' => 'nullable|integer',
                'category_id' => 'required|integer',
                'supplier_id' => 'nullable|integer',
            ]);

            // Configurar los datos del formulario
            $formParams = [
                [
                    'name' => '_method',
                    'contents' => 'PUT',
                ],
                [
                    'name' => 'name',
                    'contents' => $validatedData['name'],
                ],
                [
                    'name' => 'model',
                    'contents' => $validatedData['model'] ?? null,
                ],
                [
                    'name' => 'measurement_unit',
                    'contents' => $validatedData['measurement_unit'] ?? null,
                ],
                [
                    'name' => 'brand',
                    'contents' => $validatedData['brand'] ?? null,
                ],
                [
                    'name' => 'quantity',
                    'contents' => $validatedData['quantity'],
                ],
                [
                    'name' => 'description',
                    'contents' => $validatedData['description'] ?? null,
                ],
                [
                    'name' => 'price',
                    'contents' => $validatedData['price'],
                ],
                [
                    'name' => 'serie',
                    'contents' => $validatedData['serie'] ?? null,
                ],
                [
                    'name' => 'observations',
                    'contents' => $validatedData['observations'] ?? null,
                ],
                [
                    'name' => 'location',
                    'contents' => $validatedData['location'] ?? null,
                ],
                [
                    'name' => 'location_id',
                    'contents' => $validatedData['location_id'] ?? null,
                ],
                [
                    'name' => 'category_id',
                    'contents' => $validatedData['category_id'],
                ],
                [
                    'name' => 'supplier_id',
                    'contents' => $validatedData['supplier_id'] ?? null,
                ],
            ];

            // Comprobar si la solicitud contiene una imagen
            if ($request->hasFile('profile_image')) {
                // Procesar la nueva imagen
                $file = $request->file('profile_image');
                $filePath = $file->getPathname();
                $fileName = $file->getClientOriginalName();

                $formParams[] = [
                    'name' => 'profile_image',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => $fileName,
                ];
            }

            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/products/' . $id;

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Crear un cliente Guzzle
            $client = new Client();

            // Realizar una solicitud HTTP POST con _method=PUT para actualizar el producto
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'multipart' => $formParams,
            ]);

            // Verificar si la solicitud fue exitosa
            if ($response->getStatusCode() == 200) {
                return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
            } else {
                return back()->withInput()->withErrors('Error al actualizar el producto. Por favor, inténtalo de nuevo más tarde.');
            }
        } catch (Exception $e) {
            Log::error('Error en ProductController@update: ' . $e->getMessage());
            return back()->withInput()->withErrors('Error interno al actualizar producto: ' . $e->getMessage());
        }
    }

    public function destroy($id, Request $request) {
        try {
            // URL base de la API
            $baseApiUrl = config('app.backend_api');
            $apiUrl = $baseApiUrl . '/api/products/' . $id;

            // Obtener el token de la sesión
            $token = $request->session()->get('token');
            
            if (!$token) {
                return redirect()->route('login')->with('error', 'Sesión expirada. Por favor, inicia sesión nuevamente.');
            }

            // Realizar una solicitud HTTP DELETE a la API
            $response = Http::withToken($token)->delete($apiUrl);

            // Verificar si la solicitud fue exitosa
            if ($response->successful()) {
                return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
            } else {
                $errorMessage = $response->json()['error'] ?? 'No se pudo eliminar el producto.';
                return redirect()->back()->with('error', $errorMessage);
            }
        } catch (Exception $e) {
            Log::error('Error en ProductController@destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error interno al eliminar producto: ' . $e->getMessage());
        }
    } 
}