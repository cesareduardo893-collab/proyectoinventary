<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\EntrancesExport;

class EntranceController extends Controller {
    public function index(Request $request) {
        $baseApiUrl = config('app.backend_api');
        $apiUrl = $baseApiUrl . '/api/entrances';
        $apiSearchUrl = $baseApiUrl . '/api/searchEntrance';
        $apiGetCountMonthEntranceUrl = $baseApiUrl . '/api/GetCountMonthEntrance';
        $apiGetEntrancesCountMonthNumber = $baseApiUrl . '/api/GetEntrancesCountMonthNumber';
        $apiPostBetween = $baseApiUrl . '/api/PostBetween';
    
        $searchQuery = $request->input('query');
    
        $page = $request->input('page', 1);
        $perPage = 100;
    
        $token = $request->session()->get('token');
    
        if ($searchQuery) {
            $apiSearchUrl .= '?search=' . urlencode($searchQuery) . '&page=' . $page . '&per_page=' . $perPage;
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiSearchUrl);
        } else {
            $apiUrl .= '?page=' . $page . '&per_page=' . $perPage;
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);
        }
    
        if ($response->successful()) {
            $data = $response->json();
            if (is_array($data) && array_key_exists('data', $data)) {
                $entrances = $data['data'];
                $total = $data['total'] ?? 0;
                $currentPage = $data['current_page'] ?? 1;
                $lastPage = $data['last_page'] ?? 1;
            } else {
                $entrances = array_slice($data, ($page - 1) * $perPage, $perPage);
                $total = count($data);
                $currentPage = $page;
                $lastPage = ceil($total / $perPage);
            }
    
            $monthCountResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetEntrancesCountMonthNumber);
            $monthData = $monthCountResponse->successful() ? $monthCountResponse->json() : ['count' => 0];
    
            if ($request->has('download')) {
                $downloadType = $request->input('download');
    
                if ($downloadType === 'pdf') {
                    return $this->generatePDF($request);
                } elseif ($downloadType === 'month_pdf') {
                    $monthResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetCountMonthEntranceUrl);
    
                    if ($monthResponse->successful()) {
                        $monthData = $monthResponse->json();
    
                        $htmlContent = view('entrances.month_pdf', compact('monthData'))->render();
                        $htmlFilePath = storage_path('temp/entrances_month_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Entradas_Mes.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener las entradas del mes de la API');
                    }
                } elseif ($downloadType === 'between_dates_pdf') {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');
    
                    $dateRangeResponse = Http::withToken($token)->withOptions(['verify' => false])->post($apiPostBetween, [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
    
                    if ($dateRangeResponse->successful()) {
                        $dateRangeData = $dateRangeResponse->json();
    
                        $htmlContent = view('entrances.between_dates_pdf', compact('dateRangeData'))->render();
                        $htmlFilePath = storage_path('temp/entrances_between_dates_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Entradas_Rango_Seleccionado.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener las entradas del rango de fechas de la API');
                    }
                } elseif ($downloadType === 'between_dates_excel') {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');
    
                    $dateRangeResponse = Http::withToken($token)->withOptions(['verify' => false])->post($apiPostBetween, [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
    
                    if ($dateRangeResponse->successful()) {
                        $dateRangeData = $dateRangeResponse->json();
    
                        $filePath = storage_path('app/Entradas_Rango_Seleccionado.xlsx');
                        $export = new EntrancesExport($dateRangeData);
                        $export->export($filePath);
    
                        return response()->download($filePath)->deleteFileAfterSend(true);
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener las entradas del rango de fechas de la API');
                    }
                }
            }
    
            return view('entrances.index', compact('entrances', 'page', 'total', 'currentPage', 'lastPage', 'monthData'));
        } else {
            return 'Error: ' . $response->status();
        }
    }

    public function generatePDF(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $baseApiUrl = config('app.backend_api');

            // Obtener TODAS las entradas sin paginación
            $apiUrl = $baseApiUrl . '/api/entrances?per_page=1000';
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                // Manejar estructura de datos
                if (is_array($data) && array_key_exists('data', $data)) {
                    $entrances = $data['data'];
                } else {
                    $entrances = $data;
                }

                if (empty($entrances)) {
                    return back()->with('error', 'No hay entradas para generar el PDF.');
                }

                $pdf = Pdf::loadView('entrances.pdf', compact('entrances'));
                $pdf->setPaper('A4', 'landscape');
                return $pdf->download('entradas-' . date('Y-m-d') . '.pdf');

            } else {
                return back()->with('error', 'Error al obtener las entradas de la API');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
}