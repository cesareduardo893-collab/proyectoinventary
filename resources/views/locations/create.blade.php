@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Ubicación</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .location-form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 20px auto;
            max-width: 800px;
        }
        .location-form-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }
        .location-form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #3498db;
        }
        .location-form-section h5 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .location-btn {
            padding: 10px 25px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }
        .location-btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }
        .location-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            color: white;
        }
        .location-btn-secondary {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
            color: white;
        }
        .location-btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(149, 165, 166, 0.3);
            color: white;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #bdc3c7;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            transform: translateY(-1px);
        }
        .required-field::after {
            content: " *";
            color: #e74c3c;
        }
        .is-valid {
            border-color: #27ae60 !important;
        }
        .is-invalid {
            border-color: #e74c3c !important;
        }
        .invalid-feedback {
            display: block;
            color: #e74c3c;
            font-size: 14px;
        }
        .form-text {
            color: #7f8c8d;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="location-form-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="location-form-title mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Nueva Ubicación
                </h2>
                <a href="{{ route('locations.index') }}" class="location-btn location-btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Ubicaciones
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('locations.store') }}" class="needs-validation" novalidate>
                @csrf

                <!-- Sección de Información de Ubicación -->
                <div class="location-form-section">
                    <h5><i class="fas fa-info-circle me-2"></i>Información de la Ubicación</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sucursal" class="form-label required-field">Sucursal</label>
                            <input type="text" class="form-control" id="sucursal" name="sucursal" 
                                   value="{{ old('sucursal') }}" required maxlength="100"
                                   placeholder="Ej: Centro, Norte, Sur">
                            <div class="invalid-feedback">Por favor ingresa el nombre de la sucursal.</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="seccion_sucursal" class="form-label required-field">Sección de Sucursal</label>
                            <input type="text" class="form-control" id="seccion_sucursal" name="seccion_sucursal" 
                                   value="{{ old('seccion_sucursal') }}" required maxlength="100"
                                   placeholder="Ej: Almacén A, Bodega B">
                            <div class="invalid-feedback">Por favor ingresa la sección de la sucursal.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estante" class="form-label required-field">Estante</label>
                            <input type="text" class="form-control" id="estante" name="estante" 
                                   value="{{ old('estante') }}" required maxlength="50"
                                   placeholder="Ej: A1, B2, C3">
                            <div class="invalid-feedback">Por favor ingresa el número/letra del estante.</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="seccion_estante" class="form-label required-field">Sección del Estante</label>
                            <input type="text" class="form-control" id="seccion_estante" name="seccion_estante" 
                                   value="{{ old('seccion_estante') }}" required maxlength="50"
                                   placeholder="Ej: S1, S2, S3">
                            <div class="invalid-feedback">Por favor ingresa la sección del estante.</div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Identificación -->
                <div class="location-form-section">
                    <h5><i class="fas fa-barcode me-2"></i>Identificación</h5>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="codigo_ubicacion" class="form-label required-field">Código de Ubicación</label>
                            <input type="text" class="form-control" id="codigo_ubicacion" name="codigo_ubicacion" 
                                   value="{{ old('codigo_ubicacion') }}" required maxlength="100"
                                   placeholder="Ej: CENTRO-ALMA-A1-S1">
                            <div class="form-text">Código único para identificar la ubicación. Ej: SUCURSAL-SECCION-ESTANTE-SECCIONESTANTE</div>
                            <div class="invalid-feedback">Por favor ingresa un código único para la ubicación.</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" 
                                      rows="3" placeholder="Descripción adicional de la ubicación...">{{ old('descripcion') }}</textarea>
                            <div class="form-text">Opcional: información adicional sobre la ubicación.</div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <a href="{{ route('locations.index') }}" class="location-btn location-btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="location-btn location-btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Ubicación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Generar código de ubicación automáticamente
            function generarCodigoUbicacion() {
                const sucursal = $('#sucursal').val().toUpperCase().replace(/\s+/g, '');
                const seccionSucursal = $('#seccion_sucursal').val().toUpperCase().replace(/\s+/g, '');
                const estante = $('#estante').val().toUpperCase().replace(/\s+/g, '');
                const seccionEstante = $('#seccion_estante').val().toUpperCase().replace(/\s+/g, '');

                if (sucursal && seccionSucursal && estante && seccionEstante) {
                    const codigo = `${sucursal}-${seccionSucursal}-${estante}-${seccionEstante}`;
                    $('#codigo_ubicacion').val(codigo);
                }
            }

            // Escuchar cambios en los campos para generar el código
            $('#sucursal, #seccion_sucursal, #estante, #seccion_estante').on('input', function() {
                generarCodigoUbicacion();
            });

            // Validación del formulario
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });

            // Validación en tiempo real
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function () {
                    if (this.checkValidity()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                });

                input.addEventListener('blur', function () {
                    if (this.checkValidity()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                });
            });
        });
    </script>
</body>
</html>
@endsection