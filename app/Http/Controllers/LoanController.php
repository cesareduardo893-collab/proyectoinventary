<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LoansExport;

class LoanController extends Controller {

    public function index(Request $request) {
        if (session('role') === '2') {
            return redirect()->back()->with('error', 'No tienes permiso para acceder a esta página');
        }
    
        // URL base de la API
        $baseApiUrl = config('app.backend_api');
    
        // URL de la API de préstamos
        $apiUrl = $baseApiUrl . '/api/loans';
        $apiSearchUrl = $baseApiUrl . '/api/searchLoan';
        $apiGetCountMonthLoanUrl = $baseApiUrl . '/api/GetCountMonthLoan';
        $apiGetFinished = $baseApiUrl . '/api/GetFinished';
        $apiGetStarted = $baseApiUrl . '/api/GetStarted';
        $apiGetLoanCountMonthNumberUrl = $baseApiUrl . '/api/GetLoanCountMonthNumber';
        $apiPostBetweenLoan = $baseApiUrl . '/api/PostBetweenLoan';
        $searchQuery = $request->input('query');
    
        // Parámetros de paginación
        $page = $request->input('page', 1);
        $perPage = 100;
    
        // Obtener el token de la sesión
        $token = $request->session()->get('token');
    
        // Si hay un término de búsqueda, usar la URL de búsqueda
        if ($searchQuery) {
            $apiSearchUrl .= '?search=' . urlencode($searchQuery) . '&page=' . $page . '&per_page=' . $perPage;
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiSearchUrl);
        } else {
            $apiUrl .= '?page=' . $page . '&per_page=' . $perPage;
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);
        }
    
        // Verifica si la solicitud fue exitosa
        if ($response->successful()) {
            // Decodifica la respuesta JSON en un array asociativo
            $data = $response->json();
    
            // Verifica si la clave 'data' está presente en la respuesta
            if (is_array($data) && array_key_exists('data', $data)) {
                $loans = $data['data'];
                $total = $data['total'] ?? 0;
                $currentPage = $data['current_page'] ?? 1;
                $lastPage = $data['last_page'] ?? 1;
            } else {
                // Asume que toda la respuesta es el conjunto de datos
                $loans = array_slice($data, ($page - 1) * $perPage, $perPage);
                $total = count($data);
                $currentPage = $page;
                $lastPage = ceil($total / $perPage);
            }
    
            // Obtener el conteo de préstamos del mes actual
            $monthLoanResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetLoanCountMonthNumberUrl);
            $monthDataNumber = $monthLoanResponse->successful() ? $monthLoanResponse->json() : ['count' => 0];
    
            // Si el parámetro 'download' está presente, generar el archivo correspondiente
            if ($request->has('download')) {
                $downloadType = $request->input('download');
    
                if ($downloadType === 'pdf') {
                    return $this->generatePDF($request);
                } elseif ($downloadType === 'month_pdf') {
                    $monthResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetCountMonthLoanUrl);
    
                    if ($monthResponse->successful()) {
                        $monthData = $monthResponse->json();
    
                        $htmlContent = view('loans.month_pdf', compact('monthData'))->render();
                        $htmlFilePath = storage_path('temp/loans_month_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Prestamos_Mes.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener los préstamos del mes de la API');
                    }
                } elseif ($downloadType === 'finished_pdf') {
                    $finishedResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetFinished);
    
                    if ($finishedResponse->successful()) {
                        $finishedData = $finishedResponse->json();
    
                        $htmlContent = view('loans.finished_pdf', compact('finishedData'))->render();
                        $htmlFilePath = storage_path('temp/loans_finished_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Prestamos_Finalizados.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener los préstamos finalizados de la API');
                    }
                } elseif ($downloadType === 'started_pdf') {
                    $startedResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetStarted);
    
                    if ($startedResponse->successful()) {
                        $startedData = $startedResponse->json();
    
                        $htmlContent = view('loans.started_pdf', compact('startedData'))->render();
                        $htmlFilePath = storage_path('temp/loans_started_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Prestamos_Iniciados.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener los préstamos iniciados de la API');
                    }
                } elseif ($downloadType === 'between_dates_pdf') {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');
    
                    $dateRangeResponse = Http::withToken($token)->withOptions(['verify' => false])->post($apiPostBetweenLoan, [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
    
                    if ($dateRangeResponse->successful()) {
                        $dateRangeData = $dateRangeResponse->json();
    
                        $htmlContent = view('loans.between_dates_pdf', compact('dateRangeData'))->render();
                        $htmlFilePath = storage_path('temp/loans_between_dates_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Prestamos_Rango_Fechas.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener los préstamos del rango de fechas de la API');
                    }
                } elseif ($downloadType === 'between_dates_excel') {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');
    
                    $dateRangeResponse = Http::withToken($token)->withOptions(['verify' => false])->post($apiPostBetweenLoan, [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
    
                    if ($dateRangeResponse->successful()) {
                        $dateRangeData = $dateRangeResponse->json();
    
                        $filePath = storage_path('app/Prestamos_Rango_Seleccionado.xlsx');
                        $export = new LoansExport($dateRangeData);
                        $export->export($filePath);
    
                        return response()->download($filePath)->deleteFileAfterSend(true);
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener los préstamos del rango de fechas de la API');
                    }
                }
            }
    
            // Pasa los datos de préstamos y los parámetros de paginación a la vista y renderiza la vista
            return view('loans.index', compact('loans', 'searchQuery', 'total', 'currentPage', 'lastPage', 'monthDataNumber'));
        }
    
        // Si la solicitud no fue exitosa, redirige o muestra un mensaje de error
        return redirect()->back()->with('error', 'Error al obtener los préstamos de la API');
    }

    public function generatePDF(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $baseApiUrl = config('app.backend_api');

            // Obtener préstamos desde la API
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($baseApiUrl . '/api/loans');

            if ($response->successful()) {
                $loans = $response->json()['data'] ?? $response->json();

                // Usar DomPDF
                $pdf = Pdf::loadView('loans.pdf', [
                    'loans' => $loans,
                    'title' => 'Lista de Préstamos'
                ]);

                $pdf->setPaper('A4', 'landscape');
                return $pdf->download('prestamos.pdf');

            } else {
                return back()->with('error', 'No se pudieron obtener los préstamos para el PDF.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
    
    public function edit($id, Request $request) {
        // URL base de la API
        $baseApiUrl = config('app.backend_api');

        // URL de la API para obtener un préstamo específico
        $apiUrl = $baseApiUrl . '/api/loans/' . $id;

        // Obtener el token de la sesión
        $token = $request->session()->get('token');

        // Realiza una solicitud HTTP GET a la API para obtener los datos del préstamo
        $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);

        // Verifica si la solicitud fue exitosa
        if ($response->successful()) {
            // Decodifica la respuesta JSON en un array asociativo
            $loan = $response->json();

            // Muestra el formulario de edición con los datos del préstamo
            return view('loans.edit', compact('loan'));
        } else {
            // Manejar errores si la solicitud no fue exitosa
            return back()->withErrors('Error al obtener los datos del préstamo. Por favor, inténtalo de nuevo más tarde.');
        }
    }

    public function update(Request $request, $id) {
        $baseApiUrl = config('app.backend_api');
        $apiUrl = $baseApiUrl . '/api/comeBackLoan/' . $id;
        $token = $request->session()->get('token');

        // Incluye las observaciones en la solicitud
        $response = Http::withToken($token)->withOptions(['verify' => false])->put($apiUrl, [
            'observations' => $request->input('observations')
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            return response()->json(['error' => 'Error al devolver el préstamo.'], $response->status());
        }
    }

    public function destroy($id, Request $request) {
        // URL base de la API
        $baseApiUrl = config('app.backend_api');

        // URL de la API para eliminar un préstamo específico
        $apiUrl = $baseApiUrl . '/api/loans/' . $id;

        // Obtener el token de la sesión
        $token = $request->session()->get('token');

        // Realizar una solicitud HTTP DELETE a la API para eliminar el préstamo
        $response = Http::withToken($token)->withOptions(['verify' => false])->delete($apiUrl);

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            // Redirigir a una página de éxito o mostrar un mensaje de éxito
            return redirect()->route('loans.index')->with('success', 'Préstamo eliminado exitosamente.');
        } else {
            // Manejar errores si la solicitud no fue exitosa
            $errorMessage = $response->json()['message'] ?? 'Error al eliminar el préstamo. Por favor, inténtalo de nuevo más tarde.';
            return redirect()->route('loans.index')->with('error', $errorMessage);
        }
    }
}