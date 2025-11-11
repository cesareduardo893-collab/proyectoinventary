@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Entradas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="{{ asset('css/entrances.css') }}" rel="stylesheet">
</head>
<body class="entrances-container">
    <div class="entrances-content">
        <h1 class="entrances-title">Lista de Entradas</h1>
        
        <div class="d-flex justify-content-between align-items-center entrances-page-header mb-3">
            <div class="entrances-export-buttons">
                <!-- CORREGIDO: Botón PDF con ruta correcta -->
                <a href="{{ route('entrances.generate.pdf') }}" class="entrances-btn entrances-btn-danger">
                    <i class="fas fa-file-pdf"></i> Generar PDF
                </a>
                <a href="{{ route('entrances.index', array_merge(request()->query(), ['download' => 'excel'])) }}" class="entrances-btn entrances-btn-success entrances-excel-download">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
            </div>
        </div>

        <!-- Filtros por fecha -->
        <div class="entrances-filters-container mb-4">
            <form method="GET" action="{{ route('entrances.index') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label for="start_date" class="mr-2">Fecha inicial:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="form-group mr-3">
                    <label for="end_date" class="mr-2">Fecha final:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <button type="submit" class="entrances-btn entrances-btn-primary mr-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('entrances.index') }}" class="entrances-btn entrances-btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </form>
        </div>
        
        <div class="entrances-search-container mb-3">
            <form method="GET" action="{{ route('entrances.index') }}">
                <div class="entrances-input-group">
                    <input type="text" name="query" class="entrances-form-control form-control" placeholder="Buscar Entradas..." value="{{ request('query') }}">
                    <div class="input-group-append">
                        <button class="entrances-btn" type="submit" style="background: #000000ff; color: white;">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="entrances-table-container">
            @if(isset($entrances) && count($entrances) > 0)
            <table class="entrances-table">
                <thead>
                    <tr>
                        <th>Proyecto</th>
                        <th>Producto</th>
                        <th>Responsable</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Gastado</th>
                        <th>Ubicación</th>
                        <th>Descripción</th>
                        <th>Folio</th>
                        <th>Fecha</th>
                        <th>Nombre Cuenta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entrances as $entrance)
                        <tr>
                            <td data-label="Proyecto">{{ $entrance['project']['name'] ?? 'N/A' }}</td>
                            <td data-label="Producto">{{ $entrance['product']['name'] ?? 'N/A' }}</td>
                            <td data-label="Responsable">{{ $entrance['responsible'] ?? 'N/A' }}</td>
                            <td data-label="Cantidad">{{ number_format($entrance['quantity'] ?? 'N/A', 0, '.', ',') }}</td>
                            <td data-label="Precio">{{ number_format($entrance['price'] ?? 'N/A', 2, '.', ',') }}</td>
                            <td data-label="Gastado">{{ number_format(($entrance['price'] ?? 0) * ($entrance['quantity'] ?? 0), 2, '.', ',') }}</td>
                            <td data-label="Ubicación">{{ $entrance['product']['location'] ?? 'N/A' }}</td>
                            <td data-label="Descripción">{{ $entrance['description'] ?? 'N/A' }}</td>
                            <td data-label="Folio">{{ $entrance['folio'] ?? 'N/A' }}</td>
                            <td data-label="Fecha">{{ \Carbon\Carbon::parse($entrance['created_at'])->setTimezone('America/Mexico_City')->format('Y-m-d H:i:s') }}</td>
                            <td data-label="Nombre Cuenta">{{ $entrance['user']['name'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="entrances-pagination-container">
                <div class="entrances-pagination-info">
                    Mostrando {{ count($entrances) }} de {{ $total }} entradas
                </div>
                
                <div class="entrances-pagination">
                    @if($currentPage > 1)
                        <a href="{{ route('entrances.index', ['page' => 1, 'query' => request('query')]) }}" class="entrances-btn entrances-btn-primary entrances-btn-custom-size">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                        <a href="{{ route('entrances.index', ['page' => $currentPage - 1, 'query' => request('query')]) }}" class="entrances-btn entrances-btn-primary entrances-btn-custom-size">Anterior</a>
                    @endif
                    
                    @php
                        $showPages = 2;
                        $startPage = max(1, $currentPage - $showPages);
                        $endPage = min($lastPage, $currentPage + $showPages);
                    @endphp
                    
                    @for($i = $startPage; $i <= $endPage; $i++)
                        @if($i == $currentPage)
                            <span class="entrances-btn entrances-btn-primary active entrances-btn-custom-size">{{ $i }}</span>
                        @else
                            <a href="{{ route('entrances.index', ['page' => $i, 'query' => request('query')]) }}" class="entrances-btn entrances-btn-outline-primary entrances-btn-custom-size">{{ $i }}</a>
                        @endif
                    @endfor
                    
                    <form method="GET" action="{{ route('entrances.index') }}" class="d-inline-flex ml-2">
                        <input type="hidden" name="query" value="{{ request('query') }}">
                        <input type="number" name="page" min="1" max="{{ $lastPage }}" class="form-control entrances-page-input" placeholder="Ir a">
                        <button type="submit" class="entrances-btn entrances-btn-info entrances-btn-custom-size ml-1">Ir</button>
                    </form>
                    
                    @if($currentPage < $lastPage)
                        <a href="{{ route('entrances.index', ['page' => $currentPage + 1, 'query' => request('query')]) }}" class="entrances-btn entrances-btn-primary entrances-btn-custom-size">Siguiente</a>
                        <a href="{{ route('entrances.index', ['page' => $lastPage, 'query' => request('query')]) }}" class="entrances-btn entrances-btn-primary entrances-btn-custom-size">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    @endif
                </div>
            </div>
            @else
            <p class="text-center py-4">No se encontraron entradas.</p>
            @endif
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.es.min.js"></script>
    <script src="{{ asset('js/entrances.js') }}"></script>
</body>
</html>
@endsection