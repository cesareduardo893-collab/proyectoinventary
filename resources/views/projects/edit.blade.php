<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #343a40;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .is-invalid .form-control {
            border-color: #dc3545;
        }
        .is-valid .form-control {
            border-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Editar Proyecto</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('projects.update', $project['id']) }}" novalidate>
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nombre:</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $project['name'] }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback">Por favor, ingrese el nombre del proyecto.</div>
            </div>
            <div class="form-group">
                <label for="description">Ubicación / sub ubicacion:</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ $project['description'] }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="company_name">Nombre de la Empresa:</label>
                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ $project['company_name'] }}" required>
                @error('company_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback">Por favor, ingrese el nombre de la empresa.</div>
            </div>
            <div class="form-group">
                <label for="rfc">RFC:</label>
                <input type="text" class="form-control @error('rfc') is-invalid @enderror" id="rfc" name="rfc" value="{{ $project['rfc'] }}" maxlength="13">
                @error('rfc')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback">Seleccione o rellene RFC, máximo 13 caracteres.</div>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="noRfcCheck" name="noRfcCheck" {{ empty($project['rfc']) ? 'checked' : '' }}>
                <label for="noRfcCheck">Sin RFC</label>
            </div>
            <div class="form-group">
                <label for="address">Dirección:</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ $project['address'] }}" required>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback">Por favor, ingrese la dirección.</div>
            </div>
            <div class="form-group">
                <label for="phone_number">Teléfono:</label>
                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ $project['phone_number'] }}" maxlength="10" required>
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback">Por favor, ingrese el número de teléfono (máximo 10 caracteres).</div>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $project['email'] }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback">Por favor, ingrese el email.</div>
            </div>
            <div class="form-group">
                <label for="client_name">Nombre del Cliente:</label>
                <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name" value="{{ $project['client_name'] }}" required>
                @error('client_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback">Por favor, ingrese el nombre del cliente.</div>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Proyecto</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            toggleInputDisable('noRfcCheck', 'rfc');
        });

        function toggleInputDisable(checkboxId, inputId) {
            var checkbox = document.getElementById(checkboxId);
            var input = document.getElementById(inputId);
            if (checkbox.checked) {
                input.value = '';
                input.disabled = true;
                input.removeAttribute('required');
                input.removeAttribute('name'); // Remover el atributo 'name' para que no se envíe
            }
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    input.value = '';
                    input.disabled = true;
                    input.removeAttribute('required');
                    input.removeAttribute('name'); // Remover el atributo 'name' para que no se envíe
                } else {
                    input.disabled = false;
                    input.setAttribute('required', 'required');
                    input.setAttribute('name', 'rfc'); // Restaurar el atributo 'name' para que se envíe
                }
            });
        }

        document.querySelector('form').addEventListener('submit', function(event) {
            var requiredInputs = ['name', 'company_name', 'address', 'phone_number', 'email', 'client_name'];
            var optionalInputs = ['rfc'];
            var checkboxes = ['noRfcCheck'];

            for (var i = 0; i < requiredInputs.length; i++) {
                var input = document.getElementById(requiredInputs[i]);
                if (input.value === '') {
                    input.classList.add('is-invalid');
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            }

            for (var i = 0; i < optionalInputs.length; i++) {
                var input = document.getElementById(optionalInputs[i]);
                var checkbox = document.getElementById(checkboxes[i]);

                if (input.value === '' && !checkbox.checked) {
                    input.classList.add('is-invalid');
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }

                if (checkbox.checked) {
                    input.disabled = true;
                    input.removeAttribute('required');
                    input.removeAttribute('name'); // Remover el atributo 'name' para que no se envíe
                } else {
                    input.disabled = false;
                    input.setAttribute('required', 'required');
                    input.setAttribute('name', 'rfc'); // Restaurar el atributo 'name' para que se envíe
                }
            }

            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            this.classList.add('was-validated');
        });

        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function () {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    this.nextElementSibling.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
