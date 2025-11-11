<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor - Sistema de Inventario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .supplier-form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .form-title {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .form-section-title {
            color: #34495e;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding: 0.5rem;
            background: white;
            border-left: 4px solid #3498db;
            border-radius: 4px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: block;
        }
        .form-control, .form-select {
            border: 1px solid #bdc3c7;
            border-radius: 5px;
            padding: 0.75rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .form-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        .btn-update {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-update:hover {
            background: linear-gradient(135deg, #2980b9, #21618c);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        .btn-cancel {
            background: #95a5a6;
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-cancel:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .is-invalid {
            border-color: #e74c3c !important;
        }
        .invalid-feedback {
            display: block;
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 5px;
            padding: 1rem;
        }
        .file-input-group {
            position: relative;
        }
        .file-input-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: #e9ecef;
            border: 1px dashed #bdc3c7;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .file-input-label:hover {
            background: #dee2e6;
        }
        .file-input {
            display: none;
        }
        .file-name {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        .current-file {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        .current-file a {
            color: #155724;
            text-decoration: none;
        }
        .current-file a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    @extends('layouts.app')

    @section('title', 'Editar Proveedor')

    @section('content')
    <div class="container supplier-form-container">
        <h1 class="form-title">Editar Proveedor: {{ $supplier['nombre_razon_social'] }}</h1>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('suppliers.update', $supplier['id']) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            
            <!-- Sección: Información Básica -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-info-circle"></i> Información Básica
                </h5>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Código de Proveedor *</label>
                        <input type="text" name="codigo_proveedor" value="{{ old('codigo_proveedor', $supplier['codigo_proveedor']) }}" 
                               class="form-control @error('codigo_proveedor') is-invalid @enderror" 
                               required placeholder="Ej: PROV-001">
                        @error('codigo_proveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Proveedor *</label>
                        <select name="tipo_proveedor" class="form-select @error('tipo_proveedor') is-invalid @enderror" required>
                            <option value="">Seleccionar tipo</option>
                            @foreach($tiposProveedor as $key => $value)
                                <option value="{{ $key }}" {{ old('tipo_proveedor', $supplier['tipo_proveedor']) == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_proveedor')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre o Razón Social *</label>
                        <input type="text" name="nombre_razon_social" value="{{ old('nombre_razon_social', $supplier['nombre_razon_social']) }}" 
                               class="form-control @error('nombre_razon_social') is-invalid @enderror" 
                               required>
                        @error('nombre_razon_social')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">RFC *</label>
                        <input type="text" name="rfc_identificacion_fiscal" value="{{ old('rfc_identificacion_fiscal', $supplier['rfc_identificacion_fiscal']) }}" 
                               class="form-control @error('rfc_identificacion_fiscal') is-invalid @enderror" 
                               required style="text-transform: uppercase;">
                        @error('rfc_identificacion_fiscal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sección: Información de Contacto -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-address-book"></i> Información de Contacto
                </h5>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Persona de Contacto *</label>
                        <input type="text" name="persona_contacto" value="{{ old('persona_contacto', $supplier['persona_contacto']) }}" 
                               class="form-control @error('persona_contacto') is-invalid @enderror" required>
                        @error('persona_contacto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono Principal *</label>
                        <input type="tel" name="telefono_principal" value="{{ old('telefono_principal', $supplier['telefono_principal']) }}" 
                               class="form-control @error('telefono_principal') is-invalid @enderror" required>
                        @error('telefono_principal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" name="correo_electronico" value="{{ old('correo_electronico', $supplier['correo_electronico']) }}" 
                               class="form-control @error('correo_electronico') is-invalid @enderror" required>
                        @error('correo_electronico')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Teléfono Secundario</label>
                        <input type="tel" name="telefono_secundario" value="{{ old('telefono_secundario', $supplier['telefono_secundario']) }}" 
                               class="form-control @error('telefono_secundario') is-invalid @enderror">
                        @error('telefono_secundario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Secundario</label>
                        <input type="email" name="correo_secundario" value="{{ old('correo_secundario', $supplier['correo_secundario']) }}" 
                               class="form-control @error('correo_secundario') is-invalid @enderror">
                        @error('correo_secundario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Horarios de Atención *</label>
                        <input type="text" name="horarios_atencion" value="{{ old('horarios_atencion', $supplier['horarios_atencion']) }}" 
                               class="form-control @error('horarios_atencion') is-invalid @enderror" required>
                        @error('horarios_atencion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sección: Información Financiera -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-money-bill-wave"></i> Información Financiera
                </h5>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Cuenta Bancaria *</label>
                        <input type="text" name="cuenta_bancaria" value="{{ old('cuenta_bancaria', $supplier['cuenta_bancaria']) }}" 
                               class="form-control @error('cuenta_bancaria') is-invalid @enderror" required>
                        @error('cuenta_bancaria')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Métodos de Pago *</label>
                        <input type="text" name="metodos_pago" value="{{ old('metodos_pago', $supplier['metodos_pago']) }}" 
                               class="form-control @error('metodos_pago') is-invalid @enderror" required>
                        @error('metodos_pago')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Plazos de Crédito (días) *</label>
                        <input type="number" name="plazos_credito" value="{{ old('plazos_credito', $supplier['plazos_credito']) }}" 
                               class="form-control @error('plazos_credito') is-invalid @enderror" 
                               required min="0">
                        @error('plazos_credito')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sección: Información Adicional -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i> Información Adicional
                </h5>
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Dirección Completa *</label>
                        <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                                  required rows="3">{{ old('direccion', $supplier['direccion']) }}</textarea>
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label">Información Comercial</label>
                        <textarea name="informacion_comercial" class="form-control @error('informacion_comercial') is-invalid @enderror" 
                                  rows="3">{{ old('informacion_comercial', $supplier['informacion_comercial']) }}</textarea>
                        @error('informacion_comercial')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sección: Documentos -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-file-upload"></i> Documentos
                </h5>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Archivo Adjunto CSF</label>
                        
                        @if($supplier['archivo_adjunto_csf'])
                            <div class="current-file">
                                <i class="fas fa-file-pdf"></i>
                                Archivo actual: 
                                <a href="{{ asset('storage/' . $supplier['archivo_adjunto_csf']) }}" target="_blank">
                                    Ver archivo
                                </a>
                                @if($supplier['fecha_vigencia_csf'])
                                    <br><small>Vigencia: {{ \Carbon\Carbon::parse($supplier['fecha_vigencia_csf'])->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        @endif

                        <div class="file-input-group mt-2">
                            <label class="file-input-label">
                                <i class="fas fa-upload"></i>
                                <span id="csfFileName">Actualizar archivo CSF</span>
                                <input type="file" name="archivo_adjunto_csf" class="file-input" 
                                       accept=".pdf,.doc,.docx,.jpg,.png">
                            </label>
                            <div class="file-name" id="csfFileDisplay"></div>
                        </div>
                        @error('archivo_adjunto_csf')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Dejar vacío para mantener el archivo actual</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Opinión de Cumplimiento</label>
                        
                        @if($supplier['opinion_cumplimiento'])
                            <div class="current-file">
                                <i class="fas fa-file-pdf"></i>
                                Archivo actual: 
                                <a href="{{ asset('storage/' . $supplier['opinion_cumplimiento']) }}" target="_blank">
                                    Ver archivo
                                </a>
                                @if($supplier['fecha_vigencia_opinion'])
                                    <br><small>Vigencia: {{ \Carbon\Carbon::parse($supplier['fecha_vigencia_opinion'])->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        @endif

                        <div class="file-input-group mt-2">
                            <label class="file-input-label">
                                <i class="fas fa-upload"></i>
                                <span id="opinionFileName">Actualizar archivo de opinión</span>
                                <input type="file" name="opinion_cumplimiento" class="file-input" 
                                       accept=".pdf,.doc,.docx,.jpg,.png">
                            </label>
                            <div class="file-name" id="opinionFileDisplay"></div>
                        </div>
                        @error('opinion_cumplimiento')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-text">Dejar vacío para mantener el archivo actual</small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4 gap-3">
                <a href="{{ route('suppliers.index') }}" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-update">Actualizar Proveedor</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejo de visualización de nombres de archivos
            const csfFileInput = document.querySelector('input[name="archivo_adjunto_csf"]');
            const opinionFileInput = document.querySelector('input[name="opinion_cumplimiento"]');
            const csfFileDisplay = document.getElementById('csfFileDisplay');
            const opinionFileDisplay = document.getElementById('opinionFileDisplay');

            csfFileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    csfFileDisplay.textContent = this.files[0].name;
                } else {
                    csfFileDisplay.textContent = '';
                }
            });

            opinionFileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    opinionFileDisplay.textContent = this.files[0].name;
                } else {
                    opinionFileDisplay.textContent = '';
                }
            });

            // Validación del formulario
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validación de archivos
                const fileInputs = form.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    if (input.files.length > 0) {
                        const file = input.files[0];
                        const maxSize = 5 * 1024 * 1024; // 5MB
                        const allowedTypes = ['application/pdf', 'application/msword', 
                                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                            'image/jpeg', 'image/png'];

                        if (file.size > maxSize) {
                            alert('El archivo ' + file.name + ' excede el tamaño máximo de 5MB');
                            isValid = false;
                        }

                        if (!allowedTypes.includes(file.type)) {
                            alert('El archivo ' + file.name + ' no es un formato permitido');
                            isValid = false;
                        }
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Por favor complete todos los campos requeridos correctamente.');
                }
            });

            // Validación en tiempo real
            form.querySelectorAll('input, select, textarea').forEach(field => {
                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                    }
                });
            });
        });
    </script>
    @endsection
</body>
</html>