<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OutputsExport;

class OutputController extends Controller {
    public function index(Request $request) {
        $baseApiUrl = config('app.backend_api');
        $apiUrl = $baseApiUrl . '/api/outputs';
        $apiSearchUrl = $baseApiUrl . '/api/searchOutput';
        $apiGetCountMonthOutputUrl = $baseApiUrl . '/api/GetCountMonthOutput';
        $apiGetOutputsCountMonthNumber = $baseApiUrl . '/api/GetOutputsCountMonthNumber';
        $apiPostBetweenOutput = $baseApiUrl . '/api/PostBetweenOutput';
    
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
                $outputs = $data['data'];
                $total = $data['total'] ?? 0;
                $currentPage = $data['current_page'] ?? 1;
                $lastPage = $data['last_page'] ?? 1;
            } else {
                $outputs = array_slice($data, ($page - 1) * $perPage, $perPage);
                $total = count($data);
                $currentPage = $page;
                $lastPage = ceil($total / $perPage);
            }
    
            $monthCountResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetOutputsCountMonthNumber);
            $monthData = $monthCountResponse->successful() ? $monthCountResponse->json() : ['count' => 0];
    
            if ($request->has('download')) {
                $downloadType = $request->input('download');
    
                if ($downloadType === 'pdf') {
                    return $this->generatePDF($request);
                } elseif ($downloadType === 'month_pdf') {
                    $monthResponse = Http::withToken($token)->withOptions(['verify' => false])->get($apiGetCountMonthOutputUrl);
    
                    if ($monthResponse->successful()) {
                        $monthData = $monthResponse->json();
    
                        $htmlContent = view('outputs.month_pdf', compact('monthData'))->render();
                        $htmlFilePath = storage_path('temp/outputs_month_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Salidas_Mes.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener las salidas del mes de la API');
                    }
                } elseif ($downloadType === 'between_dates_pdf') {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');
    
                    $dateRangeResponse = Http::withToken($token)->withOptions(['verify' => false])->post($apiPostBetweenOutput, [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
    
                    if ($dateRangeResponse->successful()) {
                        $dateRangeData = $dateRangeResponse->json();
    
                        $htmlContent = view('outputs.between_dates_pdf', compact('dateRangeData'))->render();
                        $htmlFilePath = storage_path('temp/outputs_between_dates_temp_file.html');
                        file_put_contents($htmlFilePath, $htmlContent);
    
                        if (!file_exists($htmlFilePath)) {
                            return redirect()->back()->with('error', 'Error al generar el archivo HTML');
                        }
    
                        $pdfFilePath = storage_path('temp/Salidas_Rango_Seleccionado.pdf');
                        $command = '"' . env('WKHTMLTOPDF_PATH') . '" --lowquality "file:///' . $htmlFilePath . '" "' . $pdfFilePath . '"';
    
                        exec($command, $output, $returnVar);
    
                        if ($returnVar === 0) {
                            return response()->download($pdfFilePath)->deleteFileAfterSend(true);
                        } else {
                            return redirect()->back()->with('error', 'Error al generar el PDF');
                        }
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener las salidas del rango de fechas de la API');
                    }
                } elseif ($downloadType === 'between_dates_excel') {
                    $start_date = $request->input('start_date');
                    $end_date = $request->input('end_date');
    
                    $dateRangeResponse = Http::withToken($token)->withOptions(['verify' => false])->post($apiPostBetweenOutput, [
                        'start_date' => $start_date,
                        'end_date' => $end_date
                    ]);
    
                    if ($dateRangeResponse->successful()) {
                        $dateRangeData = $dateRangeResponse->json();
    
                        $filePath = storage_path('app/Salidas_Rango_Seleccionado.xlsx');
                        $export = new OutputsExport($dateRangeData);
                        $export->export($filePath);
    
                        return response()->download($filePath)->deleteFileAfterSend(true);
                    } else {
                        return redirect()->back()->with('error', 'Error al obtener las salidas del rango de fechas de la API');
                    }
                }
            }
    
            return view('outputs.index', compact('outputs', 'searchQuery', 'total', 'currentPage', 'lastPage', 'monthData'));
        }
    
        return redirect()->back()->with('error', 'Error al obtener las salidas de la API');
    }

    public function generatePDF(Request $request)
    {
        try {
            $token = $request->session()->get('token');
            $baseApiUrl = config('app.backend_api');

            // Obtener TODAS las salidas sin paginación
            $apiUrl = $baseApiUrl . '/api/outputs?per_page=1000';
            $response = Http::withToken($token)->withOptions(['verify' => false])->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                // Manejar estructura de datos
                if (is_array($data) && array_key_exists('data', $data)) {
                    $outputs = $data['data'];
                } else {
                    $outputs = $data;
                }

                if (empty($outputs)) {
                    return back()->with('error', 'No hay salidas para generar el PDF.');
                }

                $pdf = Pdf::loadView('outputs.pdf', compact('outputs'));
                $pdf->setPaper('A4', 'landscape');
                return $pdf->download('salidas-' . date('Y-m-d') . '.pdf');

            } else {
                return back()->with('error', 'Error al obtener las salidas de la API');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
}