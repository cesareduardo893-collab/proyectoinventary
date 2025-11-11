@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ubicaciones</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .locations-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 20px auto;
        }
        .locations-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }
        .locations-btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .locations-btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }
        .locations-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            color: white;
            text-decoration: none;
        }
        .locations-btn-success {
            background: linear-gradient(45deg, #27ae60, #229954);
            color: white;
        }
        .locations-btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
            color: white;
            text-decoration: none;
        }
        .locations-btn-danger {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
        }
        .locations-btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
            color: white;
            text-decoration: none;
        }
        .locations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .locations-table th {
            background: linear-gradient(45deg, #34495e, #2c3e50);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        .locations-table td {
            padding: 15px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
        }
        .locations-table tr:hover {
            background-color: #f8f9fa;
        }
        .locations-search-container {
            margin-bottom: 25px;
        }
        .locations-search-input {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #bdc3c7;
            transition: all 0.3s ease;
        }
        .locations-search-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .locations-action-buttons {
            display: flex;
            gap: 8px;
        }
        .locations-action-btn {
            padding: 8px 12px;
            border-radius: 5px;
            border: none;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .locations-btn-edit {
            background: #f39c12;
        }
        .locations-btn-edit:hover {
            background: #e67e22;
            transform: translateY(-1px);
            color: white;
            text-decoration: none;
        }
        .locations-btn-delete {
            background: #e74c3c;
        }
        .locations-btn-delete:hover {
            background: #c0392b;
            transform: translateY(-1px);
            color: white;
        }
        .locations-no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        .locations-no-data i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #bdc3c7;
        }
        .locations-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .locations-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .locations-badge-active {
            background: #d4edda;
            color: #155724;
        }
        .locations-badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .locations-export-buttons {
            display: flex;
            gap: 0.5rem;
        }
        @media (max-width: 768px) {
            .locations-header-actions {
                flex-direction: column;
                align-items: flex-start;
            }
            .locations-export-buttons {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="locations-container">
            <h1 class="locations-title">
                <i class="fas fa-map-marker-alt me-2"></i>Gestión de Ubicaciones
            </h1>

            <div class="locations-header-actions">
                <a href="{{ route('locations.create') }}" class="locations-btn locations-btn-primary">
                    <i class="fas fa-plus me-2"></i>Nueva Ubicación
                </a>
                
                <div class="locations-export-buttons">
                    <a href="{{ route('locations.index', ['download' => 'pdf']) }}" class="locations-btn locations-btn-danger">
                        <i class="fas fa-file-pdf me-2"></i>PDF
                    </a>
                    <a href="{{ route('locations.index', ['download' => 'excel']) }}" class="locations-btn locations-btn-success">
                        <i class="fas fa-file-excel me-2"></i>Excel
                    </a>
                </div>
            </div>

            <div class="locations-search-container">
                <form method="GET" action="{{ route('locations.index') }}">
                    <div class="input-group">
                        <input type="text" name="query" class="form-control locations-search-input" 
                               placeholder="Buscar ubicaciones..." value="{{ request('query') }}">
                        <div class="input-group-append">
                            <button class="locations-btn locations-btn-primary" type="submit">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($locations) && count($locations) > 0)
                <div class="table-responsive">
                    <table class="locations-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Sucursal</th>
                                <th>Sección</th>
                                <th>Estante</th>
                                <th>Sección Estante</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $location)
                                <tr>
                                    <td>
                                        <strong>{{ $location['codigo_ubicacion'] }}</strong>
                                    </td>
                                    <td>{{ $location['sucursal'] }}</td>
                                    <td>{{ $location['seccion_sucursal'] }}</td>
                                    <td>{{ $location['estante'] }}</td>
                                    <td>{{ $location['seccion_estante'] }}</td>
                                    <td>
                                        @if($location['descripcion'])
                                            {{ Str::limit($location['descripcion'], 50) }}
                                        @else
                                            <span class="text-muted">Sin descripción</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($location['activo'])
                                            <span class="locations-badge locations-badge-active">
                                                <i class="fas fa-check-circle me-1"></i>Activo
                                            </span>
                                        @else
                                            <span class="locations-badge locations-badge-inactive">
                                                <i class="fas fa-times-circle me-1"></i>Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="locations-action-buttons">
                                            <a href="{{ route('locations.edit', $location['id']) }}" 
                                               class="locations-action-btn locations-btn-edit"
                                               title="Editar ubicación">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('locations.destroy', $location['id']) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="locations-action-btn locations-btn-delete"
                                                        title="Eliminar ubicación"
                                                        onclick="return confirm('¿Estás seguro de que quieres eliminar esta ubicación?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="locations-no-data">
                    <i class="fas fa-map-marked-alt"></i>
                    <h4>No se encontraron ubicaciones</h4>
                    <p class="text-muted">
                        @if(request('query'))
                            No hay resultados para "{{ request('query') }}"
                        @else
                            No hay ubicaciones registradas en el sistema.
                        @endif
                    </p>
                    @if(request('query'))
                        <a href="{{ route('locations.index') }}" class="locations-btn locations-btn-primary">
                            <i class="fas fa-undo me-2"></i>Ver todas las ubicaciones
                        </a>
                    @else
                        <a href="{{ route('locations.create') }}" class="locations-btn locations-btn-primary">
                            <i class="fas fa-plus me-2"></i>Crear primera ubicación
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
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
                        cancelButtonColor: '#7f8c8d',
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

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>
</body>
</html>
@endsection