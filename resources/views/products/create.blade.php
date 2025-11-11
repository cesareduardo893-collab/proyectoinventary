<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de Productos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .product-form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 20px auto;
            max-width: 900px;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #007bff;
        }
        .form-section h5 {
            color: #495057;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        .image-preview-container {
            text-align: center;
            margin: 15px 0;
        }
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 10px;
        }
        .btn-custom {
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .btn-custom-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
        }
        .btn-custom-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.3);
        }
        .btn-custom-secondary {
            background: linear-gradient(45deg, #6c757d, #545b62);
            border: none;
            color: white;
        }
        .btn-custom-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108,117,125,0.3);
            color: white;
        }
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
            transform: translateY(-1px);
        }
        .price-input-group {
            position: relative;
        }
        .price-input-group::before {
            content: "$";
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #495057;
            font-weight: bold;
            z-index: 3;
        }
        .price-input-group input {
            padding-left: 30px;
        }
        .is-valid {
            border-color: #28a745 !important;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
        .invalid-feedback {
            display: block;
        }
        .hidden-field {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="product-form-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="fas fa-box text-primary me-2"></i>Alta de Productos</h2>
                <a href="{{ route('products.index') }}" class="btn btn-custom-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Inventario
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

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Debug info (puedes remover esto después) -->
            @if(empty($suppliers) || empty($categories) || empty($locations))
                <div class="alert alert-warning">
                    <strong>Debug Info:</strong><br>
                    Categorías: {{ count($categories ?? []) }}<br>
                    Proveedores: {{ count($suppliers ?? []) }}<br>
                    Ubicaciones: {{ count($locations ?? []) }}
                </div>
            @endif

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf

                <!-- Campo cantidad oculto con valor 0 -->
                <input type="hidden" id="quantity" name="quantity" value="0">

                <!-- Sección de Información Básica -->
                <div class="form-section">
                    <h5><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label required-field">Nombre del Producto</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required maxlength="50">
                            <div class="invalid-feedback">Por favor ingresa el nombre del producto.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="brand" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand') }}" maxlength="50">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="model" name="model" value="{{ old('model') }}" maxlength="50">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="serie" class="form-label">Número de Serie</label>
                            <input type="text" class="form-control" id="serie" name="serie" value="{{ old('serie') }}" maxlength="40">
                        </div>
                    </div>
                </div>

                <!-- Sección de Inventario y Precio -->
                <div class="form-section">
                    <h5><i class="fas fa-calculator me-2"></i>Inventario y Precio</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="measurement_unit" class="form-label">Unidad de Medida</label>
                            <input type="text" class="form-control" id="measurement_unit" name="measurement_unit" value="{{ old('measurement_unit') }}" maxlength="15">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label required-field">Precio</label>
                            <div class="price-input-group">
                                <input type="text" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
                            </div>
                            <div class="invalid-feedback">Por favor ingresa el precio.</div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Categorización -->
                <div class="form-section">
                    <h5><i class="fas fa-tags me-2"></i>Categorización</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label required-field">Categoría</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category['id'] }}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>
                                        {{ $category['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Por favor selecciona una categoría.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="supplier_id" class="form-label">Proveedor</label>
                            <select class="form-select" id="supplier_id" name="supplier_id">
                                <option value="">Selecciona un proveedor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier['id'] }}" {{ old('supplier_id') == $supplier['id'] ? 'selected' : '' }}>
                                        {{ $supplier['nombre_razon_social'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sección de Ubicación -->
                <div class="form-section">
                    <h5><i class="fas fa-map-marker-alt me-2"></i>Ubicación</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="location_id" class="form-label">Ubicación Detallada</label>
                            <select class="form-select" id="location_id" name="location_id">
                                <option value="">Selecciona una ubicación</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location['id'] }}" {{ old('location_id') == $location['id'] ? 'selected' : '' }}>
                                        @if(isset($location['ubicacion_completa']))
                                            {{ $location['ubicacion_completa'] }}
                                        @else
                                            {{ $location['sucursal'] }} - {{ $location['seccion_sucursal'] }} - Estante {{ $location['estante'] }} - Sección {{ $location['seccion_estante'] }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Sección de Descripción e Imagen -->
                <div class="form-section">
                    <h5><i class="fas fa-edit me-2"></i>Descripción e Imagen</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="3" maxlength="500">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="observations" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observations" name="observations" rows="2" maxlength="50">{{ old('observations') }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="profile_image" class="form-label">Imagen del Producto</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/jpeg,image/png,image/gif,image/svg+xml">
                            <small class="form-text text-muted">Formatos permitidos: JPEG, PNG, GIF, SVG. Tamaño máximo: 2MB</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="image-preview-container">
                                <img id="imagePreview" src="#" alt="Vista previa de la imagen" class="image-preview" style="display: none;">
                                <div id="noImagePreview" class="text-muted">
                                    <i class="fas fa-image fa-3x mb-2"></i>
                                    <p>Vista previa de la imagen</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-custom-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-custom-primary">
                        <i class="fas fa-save me-2"></i>Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Formatear precio con comas
            $('#price').on('input', function() {
                let value = $(this).val().replace(/,/g, '');
                if (!isNaN(value) && value !== '') {
                    $(this).val(parseFloat(value).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                }
            });

            // Vista previa de imagen
            $('#profile_image').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).show();
                        $('#noImagePreview').hide();
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').hide();
                    $('#noImagePreview').show();
                }
            });

            // Validación del formulario
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                let allValid = true;

                // Validar nombre
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    nameInput.classList.add('is-invalid');
                    allValid = false;
                } else {
                    nameInput.classList.remove('is-invalid');
                    nameInput.classList.add('is-valid');
                }

                // Validar precio
                const priceInput = document.getElementById('price');
                if (!priceInput.value || isNaN(priceInput.value.replace(/,/g, ''))) {
                    priceInput.classList.add('is-invalid');
                    allValid = false;
                } else {
                    priceInput.classList.remove('is-invalid');
                    priceInput.classList.add('is-valid');
                }

                // Validar categoría
                const categorySelect = document.getElementById('category_id');
                if (!categorySelect.value) {
                    categorySelect.classList.add('is-invalid');
                    allValid = false;
                } else {
                    categorySelect.classList.remove('is-invalid');
                    categorySelect.classList.add('is-valid');
                }

                // Asegurar que quantity tenga valor 0
                const quantityInput = document.getElementById('quantity');
                quantityInput.value = '0';

                // Remover comas del precio antes de enviar
                if (allValid) {
                    priceInput.value = priceInput.value.replace(/,/g, '');
                } else {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Por favor complete todos los campos requeridos correctamente.');
                }

                this.classList.add('was-validated');
            });

            // Validación en tiempo real
            document.querySelectorAll('.form-control, .form-select').forEach(input => {
                input.addEventListener('input', function () {
                    if (this.value && this.value !== '') {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        if (this.hasAttribute('required') && this.classList.contains('was-validated')) {
                            this.classList.add('is-invalid');
                        }
                    }
                });

                input.addEventListener('change', function () {
                    if (this.value && this.value !== '') {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                        if (this.hasAttribute('required') && this.classList.contains('was-validated')) {
                            this.classList.add('is-invalid');
                        }
                    }
                });
            });

            // Inicializar validación para campos con valores preestablecidos
            document.querySelectorAll('.form-control, .form-select').forEach(input => {
                if (input.value && input.value !== '') {
                    input.classList.add('is-valid');
                }
            });
        });
    </script>
</body>
</html>