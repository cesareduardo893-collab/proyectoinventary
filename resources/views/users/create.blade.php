<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        .password-toggle {
            cursor: pointer;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        @media (max-width: 768px) {
            .form-table, .form-row, .form-cell {
                display: block;
            }
            .form-cell {
                padding: 8px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container employee-form-container">
        <h1 class="form-title">Nuevo Empleado</h1>
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="form-section">
                <h5 class="form-section-title">Información del Empleado</h5>
                <div class="form-table">
                    <div class="form-row">
                        <div class="form-cell">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-cell">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-cell">
                            <label class="form-label">Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('password', this)">
                                        <i class="fas fa-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h5 class="form-section-title">Tipo de empleado</h5>
                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="">Selecciona un rol</option>
                        <option value="1" {{ old('role') == 1 ? 'selected' : '' }}>Administrador</option>
                        <option value="2" {{ old('role') == 2 ? 'selected' : '' }}>Trabajador</option>
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <h5 class="form-section-title">Verificación de Administrador</h5>
                <div class="form-group">
                    <label class="form-label">Contraseña de Administrador</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                        <div class="input-group-append">
                            <span class="input-group-text password-toggle" onclick="togglePasswordVisibility('admin_password', this)">
                                <i class="fas fa-eye-slash"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('users.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
                <button type="submit" class="btn btn-agregar">Agregar</button>
            </div>
        </form>
    </div>

    <!-- JavaScript para Bootstrap y dependencias -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <script>
        function togglePasswordVisibility(fieldId, toggleElement) {
            var passwordField = document.getElementById(fieldId);
            var passwordFieldType = passwordField.getAttribute('type');
            var toggleIcon = toggleElement.querySelector('i');
            
            if (passwordFieldType === 'password') {
                passwordField.setAttribute('type', 'text');
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordField.setAttribute('type', 'password');
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
