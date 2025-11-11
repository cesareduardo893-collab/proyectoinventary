@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Proveedores</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .suppliers-container {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }
        .suppliers-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        .suppliers-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 2rem;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
        }
        .suppliers-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .suppliers-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .suppliers-btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }
        .suppliers-btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #21618c);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            color: white;
            text-decoration: none;
        }
        .suppliers-btn-success {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }
        .suppliers-btn-success:hover {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .suppliers-btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }
        .suppliers-btn-danger:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .suppliers-search-container {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .suppliers-input-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .suppliers-form-control {
            flex: 1;
            min-width: 200px;
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            padding: 0.75rem;
        }
        .suppliers-filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        .suppliers-table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .suppliers-table {
            width: 100%;
            border-collapse: collapse;
        }
        .suppliers-table th {
            background: #34495e;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        .suppliers-table td {
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
        }
        .suppliers-table tr:hover {
            background: #f8f9fa;
        }
        .suppliers-action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .suppliers-table-action-btn {
            padding: 0.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
        }
        .suppliers-table-btn-view {
            background: #17a2b8;
            color: white;
        }
        .suppliers-table-btn-view:hover {
            background: #138496;
            transform: scale(1.1);
        }
        .suppliers-table-btn-edit {
            background: #3498db;
            color: white;
        }
        .suppliers-table-btn-edit:hover {
            background: #2980b9;
            transform: scale(1.1);
        }
        .suppliers-table-btn-delete {
            background: #e74c3c;
            color: white;
        }
        .suppliers-table-btn-delete:hover {
            background: #c0392b;
            transform: scale(1.1);
        }
        .suppliers-pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        .suppliers-pagination-info {
            color: #6c757d;
            font-weight: 500;
        }
        .suppliers-pagination {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .suppliers-btn-custom-size {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .suppliers-btn-outline-primary {
            background: transparent;
            border: 2px solid #3498db;
            color: #3498db;
        }
        .suppliers-btn-outline-primary:hover {
            background: #3498db;
            color: white;
        }
        .suppliers-page-input {
            width: 80px;
            margin: 0 0.5rem;
        }
        .suppliers-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge-nacional { background: #27ae60; color: white; }
        .badge-internacional { background: #e67e22; color: white; }
        .badge-servicios { background: #9b59b6; color: white; }
        .badge-productos { background: #3498db; color: white; }
        .badge-gobierno { background: #e74c3c; color: white; }
        @media (max-width: 768px) {
            .suppliers-table {
                display: block;
                overflow-x: auto;
            }
            .suppliers-pagination-container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body class="suppliers-container">
    <div class="suppliers-content">
        <h1 class="suppliers-title">Lista de Proveedores</h1>
        
        <div class="suppliers-page-header">
            @if (session('role') === '1' || session('role') === '0')
            <a href="{{ route('suppliers.create') }}" class="suppliers-btn suppliers-btn-primary">
                <i class="fas fa-plus"></i> Agregar Proveedor
            </a>
            @endif
            
            <div class="suppliers-export-buttons">
                <a href="{{ route('suppliers.index', array_merge(request()->query(), ['download' => 'pdf'])) }}" class="suppliers-btn suppliers-btn-danger">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('suppliers.index', array_merge(request()->query(), ['download' => 'excel'])) }}" class="suppliers-btn suppliers-btn-success">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
            </div>
        </div>
        
        <div class="suppliers-search-container">
            <form method="GET" action="{{ route('suppliers.index') }}">
                <div class="suppliers-input-group">
                    <input type="text" name="query" class="suppliers-form-control form-control" 
                           placeholder="Buscar por nombre, RFC o código..." value="{{ request('query') }}">
                    <select name="tipo_proveedor" class="suppliers-form-control form-control">
                        <option value="">Todos los tipos</option>
                        @foreach($tiposProveedor as $key => $value)
                            <option value="{{ $key }}" {{ request('tipo_proveedor') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    <select name="order" class="suppliers-form-control form-control">
                        <option value="recent" {{ request('order') == 'recent' ? 'selected' : '' }}>Más recientes</option>
                        <option value="oldest" {{ request('order') == 'oldest' ? 'selected' : '' }}>Más antiguos</option>
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Nombre A-Z</option>
                        <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Nombre Z-A</option>
                    </select>
                    <button class="suppliers-btn suppliers-btn-primary" type="submit">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                    <a href="{{ route('suppliers.index') }}" class="suppliers-btn suppliers-btn-secondary">
                        <i class="fas fa-refresh"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>
        
        <div class="suppliers-table-container">
            @if(isset($suppliers) && count($suppliers) > 0)
            <table class="suppliers-table">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Razón Social</th>
                    <th>RFC</th>
                    <th>Tipo</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Plazos Crédito</th>
                    @if (session('role') === '1' || session('role') === '0')
                    <th>Acciones</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                        <tr>
                            <td data-label="Código">
                                <strong>{{ $supplier['codigo_proveedor'] }}</strong>
                            </td>
                            <td data-label="Razón Social">{{ $supplier['nombre_razon_social'] }}</td>
                            <td data-label="RFC">{{ $supplier['rfc_identificacion_fiscal'] }}</td>
                            <td data-label="Tipo">
                                <span class="suppliers-badge badge-{{ strtolower($supplier['tipo_proveedor']) }}">
                                    {{ $supplier['tipo_proveedor'] }}
                                </span>
                            </td>
                            <td data-label="Contacto">{{ $supplier['persona_contacto'] }}</td>
                            <td data-label="Teléfono">{{ $supplier['telefono_principal'] }}</td>
                            <td data-label="Email">{{ $supplier['correo_electronico'] }}</td>
                            <td data-label="Plazos Crédito">{{ $supplier['plazos_credito'] }} días</td>
                            @if (session('role') === '1' || session('role') === '0')
                            <td data-label="Acciones">
                                <div class="suppliers-action-buttons">
                                    <a href="{{ route('suppliers.show', $supplier['id']) }}" 
                                       class="suppliers-table-action-btn suppliers-table-btn-view"
                                       title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier['id']) }}" 
                                       class="suppliers-table-action-btn suppliers-table-btn-edit"
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="suppliers-table-action-btn suppliers-table-btn-delete"
                                                title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de eliminar este proveedor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="suppliers-pagination-container">
                <div class="suppliers-pagination-info">
                    Mostrando {{ count($suppliers) }} de {{ $total }} proveedores - Página {{ $currentPage }} de {{ $lastPage }}
                </div>
                
                <div class="suppliers-pagination">
                    @if($currentPage > 1)
                        <a href="{{ route('suppliers.index', array_merge(request()->query(), ['page' => 1])) }}" 
                           class="suppliers-btn suppliers-btn-primary suppliers-btn-custom-size">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                        <a href="{{ route('suppliers.index', array_merge(request()->query(), ['page' => $currentPage - 1])) }}" 
                           class="suppliers-btn suppliers-btn-primary suppliers-btn-custom-size">
                            Anterior
                        </a>
                    @endif
                    
                    @php
                        $showPages = 2;
                        $startPage = max(1, $currentPage - $showPages);
                        $endPage = min($lastPage, $currentPage + $showPages);
                    @endphp
                    
                    @for($i = $startPage; $i <= $endPage; $i++)
                        @if($i == $currentPage)
                            <span class="suppliers-btn suppliers-btn-primary active suppliers-btn-custom-size">{{ $i }}</span>
                        @else
                            <a href="{{ route('suppliers.index', array_merge(request()->query(), ['page' => $i])) }}" 
                               class="suppliers-btn suppliers-btn-outline-primary suppliers-btn-custom-size">{{ $i }}</a>
                        @endif
                    @endfor
                    
                    <form method="GET" action="{{ route('suppliers.index') }}" class="d-inline-flex ml-2">
                        @foreach(request()->query() as $key => $value)
                            @if($key != 'page')
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="number" name="page" min="1" max="{{ $lastPage }}" 
                               class="form-control suppliers-page-input" placeholder="Ir a">
                        <button type="submit" class="suppliers-btn suppliers-btn-primary suppliers-btn-custom-size ml-1">
                            Ir
                        </button>
                    </form>
                    
                    @if($currentPage < $lastPage)
                        <a href="{{ route('suppliers.index', array_merge(request()->query(), ['page' => $currentPage + 1])) }}" 
                           class="suppliers-btn suppliers-btn-primary suppliers-btn-custom-size">
                            Siguiente
                        </a>
                        <a href="{{ route('suppliers.index', array_merge(request()->query(), ['page' => $lastPage])) }}" 
                           class="suppliers-btn suppliers-btn-primary suppliers-btn-custom-size">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    @endif
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No se encontraron proveedores.</p>
                @if (session('role') === '1' || session('role') === '0')
                <a href="{{ route('suppliers.create') }}" class="suppliers-btn suppliers-btn-primary mt-2">
                    <i class="fas fa-plus"></i> Agregar Primer Proveedor
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                timer: 3000
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                timer: 3000
            });
        @endif

        // Confirmación para eliminar
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[action*="destroy"]');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74c3c',
                        cancelButtonColor: '#95a5a6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
@endsection