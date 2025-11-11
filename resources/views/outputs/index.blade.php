@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Salidas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="{{ asset('css/outputs.css') }}" rel="stylesheet">
</head>
<body class="outputs-container">
    <div class="outputs-content">
        <h1 class="outputs-title">Lista de Salidas</h1>
        
        <div class="d-flex justify-content-between align-items-center outputs-page-header mb-3">
            <div class="outputs-export-buttons">
                <!-- CORREGIDO: Botón PDF con ruta correcta -->
                <a href="{{ route('outputs.generate.pdf') }}" class="outputs-btn outputs-btn-danger">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </a>
                <a href="{{ route('outputs.index', array_merge(request()->query(), ['download' => 'excel'])) }}" class="outputs-btn outputs-btn-success outputs-excel-download">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
            </div>
        </div>

        <!-- Filtros por fecha -->
        <div class="outputs-filters-container mb-4">
            <form method="GET" action="{{ route('outputs.index') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label for="start_date" class="mr-2">Fecha inicial:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="form-group mr-3">
                    <label for="end_date" class="mr-2">Fecha final:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <button type="submit" class="outputs-btn outputs-btn-primary mr-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('outputs.index') }}" class="outputs-btn outputs-btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </form>
        </div>
        
        <div class="outputs-search-container mb-3">
            <form method="GET" action="{{ route('outputs.index') }}">
                <div class="outputs-input-group">
                    <input type="text" name="query" class="outputs-form-control form-control" placeholder="Buscar Salidas..." value="{{ request('query') }}">
                    <div class="input-group-append">
                        <button class="outputs-btn" type="submit" style="background: #000000ff; color: white;">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="outputs-table-container">
            @if(isset($outputs) && count($outputs) > 0)
            <table class="outputs-table">
                <thead>
                    <tr>
                        <th>Proyecto</th>
                        <th>Producto</th>
                        <th>Responsable</th>
                        <th>Cantidad</th>
                        <th>Ubicación</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Nombre Cuenta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($outputs as $output)
                        <tr>
                            <td data-label="Proyecto">{{ $output['project']['name'] ?? 'N/A' }}</td>
                            <td data-label="Producto">{{ $output['product']['name'] ?? 'N/A' }}</td>
                            <td data-label="Responsable">{{ $output['responsible'] ?? 'N/A' }}</td>
                            <td data-label="Cantidad">{{ number_format($output['quantity'] ?? 'N/A', 0, '.', ',') }}</td>
                            <td data-label="Ubicación">{{ $output['product']['location'] ?? 'N/A' }}</td>
                            <td data-label="Descripción">{{ $output['description'] ?? 'N/A' }}</td>
                            <td data-label="Fecha">{{ \Carbon\Carbon::parse($output['created_at'])->setTimezone('America/Mexico_City')->format('Y-m-d H:i:s') }}</td>
                            <td data-label="Nombre Cuenta">{{ $output['user']['name'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="outputs-pagination-container">
                <div class="outputs-pagination-info">
                    Mostrando {{ count($outputs) }} de {{ $total }} salidas
                </div>
                
                <div class="outputs-pagination">
                    @if($currentPage > 1)
                        <a href="{{ route('outputs.index', ['page' => 1, 'query' => request('query')]) }}" class="outputs-btn outputs-btn-primary outputs-btn-custom-size">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                        <a href="{{ route('outputs.index', ['page' => $currentPage - 1, 'query' => request('query')]) }}" class="outputs-btn outputs-btn-primary outputs-btn-custom-size">Anterior</a>
                    @endif
                    
                    @php
                        $showPages = 2;
                        $startPage = max(1, $currentPage - $showPages);
                        $endPage = min($lastPage, $currentPage + $showPages);
                    @endphp
                    
                    @for($i = $startPage; $i <= $endPage; $i++)
                        @if($i == $currentPage)
                            <span class="outputs-btn outputs-btn-primary active outputs-btn-custom-size">{{ $i }}</span>
                        @else
                            <a href="{{ route('outputs.index', ['page' => $i, 'query' => request('query')]) }}" class="outputs-btn outputs-btn-outline-primary outputs-btn-custom-size">{{ $i }}</a>
                        @endif
                    @endfor
                    
                    <form method="GET" action="{{ route('outputs.index') }}" class="d-inline-flex ml-2">
                        <input type="hidden" name="query" value="{{ request('query') }}">
                        <input type="number" name="page" min="1" max="{{ $lastPage }}" class="form-control outputs-page-input" placeholder="Ir a">
                        <button type="submit" class="outputs-btn outputs-btn-info outputs-btn-custom-size ml-1">Ir</button>
                    </form>
                    
                    @if($currentPage < $lastPage)
                        <a href="{{ route('outputs.index', ['page' => $currentPage + 1, 'query' => request('query')]) }}" class="outputs-btn outputs-btn-primary outputs-btn-custom-size">Siguiente</a>
                        <a href="{{ route('outputs.index', ['page' => $lastPage, 'query' => request('query')]) }}" class="outputs-btn outputs-btn-primary outputs-btn-custom-size">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    @endif
                    
                    <span class="ml-2">Página {{ $currentPage }} de {{ $lastPage }}</span>
                </div>
            </div>            
            @else
            <p>No se encontraron salidas.</p>
            @endif
        </div>
    </div>

    <!-- Variables para JavaScript -->
    <script>
        window.outputsLastPage = {{ $lastPage }};
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.es.min.js"></script>
    <script src="{{ asset('js/outputs.js') }}"></script>
</body>
</html>
@endsection