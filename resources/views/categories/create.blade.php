<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Categoría</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .employee-form-container {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .form-title {
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: bold;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            text-align: center;
        }
        .form-section {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .form-section-title {
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .form-table {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 15px;
        }
        .form-row {
            display: table-row;
        }
        .form-cell {
            display: table-cell;
            padding: 10px 15px;
            vertical-align: top;
            width: 50%;
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .btn-agregar {
            background-color: #3498db;
            border-color: #3498db;
            font-weight: 500;
            padding: 8px 25px;
            border-radius: 5px;
        }
        .btn-agregar:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        .char-counter {
            font-size: 0.8rem;
            text-align: right;
        }
        @media (max-width: 768px) {
            .form-table, .form-row, .form-cell {
                display: block;
            }
            .form-cell {
                padding: 8px 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container employee-form-container">
        <h1 class="form-title">Crear Categoría</h1>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('categories.store') }}" novalidate>
            @csrf
            
            <div class="form-section">
                <h5 class="form-section-title">Información de la Categoría</h5>
                <div class="form-table">
                    <div class="form-row">
                        <div class="form-cell">
                            <label class="form-label">Nombre:</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100" class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-cell">
                            <label class="form-label">Descripción:</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" maxlength="500" rows="4">{{ old('description') }}</textarea>
                            <div class="char-counter text-muted">0/500 caracteres</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-cell">
                            <label class="form-label">Materiales:</label>
                            <textarea id="materials" name="materials" class="form-control @error('materials') is-invalid @enderror" maxlength="500" rows="4" placeholder="Ej: algodón, poliéster, lino">{{ old('materials') }}</textarea>
                            <div class="char-counter text-muted">0/500 caracteres</div>
                            @error('materials')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('categories.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
                <button type="submit" class="btn btn-agregar">Crear Categoría</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            var requiredInputs = ['name'];
            var allValid = true;

            for (var i = 0; i < requiredInputs.length; i++) {
                var input = document.getElementById(requiredInputs[i]);
                if (input.value === '' || input.value.length > 100) {
                    input.classList.add('is-invalid');
                    allValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            }

            var descriptionInput = document.getElementById('description');
            if (descriptionInput.value.length > 500) {
                descriptionInput.classList.add('is-invalid');
                allValid = false;
            } else {
                descriptionInput.classList.remove('is-invalid');
            }

            var materialsInput = document.getElementById('materials');
            if (materialsInput.value.length > 500) {
                materialsInput.classList.add('is-invalid');
                allValid = false;
            } else {
                materialsInput.classList.remove('is-invalid');
            }

            if (!allValid) {
                event.preventDefault();
                event.stopPropagation();
            }

            this.classList.add('was-validated');
        });

        // Función para actualizar contador de caracteres
        function updateCharCounter(textarea) {
            var charCount = textarea.value.length;
            var maxLength = 500;
            var counter = textarea.nextElementSibling;
            
            if (counter && counter.classList.contains('char-counter')) {
                counter.textContent = charCount + '/' + maxLength + ' caracteres';
                
                if (charCount > maxLength) {
                    counter.classList.add('text-danger');
                    counter.classList.remove('text-muted');
                } else {
                    counter.classList.remove('text-danger');
                    counter.classList.add('text-muted');
                }
            }
        }

        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function () {
                if (this.value !== '' && this.value.length <= this.maxLength) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
                
                // Contador de caracteres para textareas
                if (this.id === 'description' || this.id === 'materials') {
                    updateCharCounter(this);
                }
            });
            
            // Inicializar contador de caracteres para textareas
            if (input.id === 'description' || input.id === 'materials') {
                updateCharCounter(input);
            }
        });
    </script>
</body>
</html>