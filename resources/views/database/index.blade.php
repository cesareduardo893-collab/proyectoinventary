@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Base de Datos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .database-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }
        .database-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        .database-card-header {
            background: linear-gradient(135deg, #000000, #434343);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
            font-weight: 600;
        }
        .database-card-body {
            padding: 25px;
        }
        .btn-database {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-backup {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .btn-import {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }
        .btn-reset {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }
        .btn-database:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        .alert-area {
            min-height: 60px;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        .feature-text {
            font-size: 0.9rem;
            color: #6c757d;
            line-height: 1.5;
        }
        .danger-zone {
            border-left: 4px solid #dc3545;
            background: #fff5f5;
        }
        .form-control-file {
            border: 2px dashed #dee2e6;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            background: #f8f9fa;
        }
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .user-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.85rem;
        }
        .redirect-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="database-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="database-card">
                    <div class="card-header database-card-header">
                        <h4 class="mb-0"><i class="fas fa-database"></i> Gestión de Base de Datos</h4>
                    </div>
                    <div class="card-body database-card-body">
                        
                        <!-- Información del usuario -->
                        <div class="user-info mb-4">
                            <small>
                                <i class="fas fa-user"></i> Usuario: {{ session('user_name') ?? 'Administrador' }} | 
                                <i class="fas fa-key"></i> Rol: Administrador
                            </small>
                        </div>

                        <!-- Backup Section -->
                        <div class="row mb-5">
                            <div class="col-md-8">
                                <h5><i class="fas fa-download text-success"></i> Crear Respaldo</h5>
                                <p class="feature-text">
                                    Genera un archivo de respaldo completo de la base de datos. El archivo se descargará automáticamente en tu equipo.
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <button id="backupBtn" class="btn btn-database btn-backup">
                                    <i class="fas fa-download"></i> Generar Backup
                                    <div class="loading-spinner" id="backupSpinner"></div>
                                </button>
                            </div>
                            <div class="col-12 mt-2">
                                <div id="backupMessage" class="alert-area"></div>
                            </div>
                        </div>

                        <!-- Import Section -->
                        <div class="row mb-5">
                            <div class="col-md-8">
                                <h5><i class="fas fa-upload text-primary"></i> Importar Base de Datos</h5>
                                <p class="feature-text">
                                    Restaura la base de datos desde un archivo SQL. <strong class="text-danger">Esta acción reemplazará todos los datos actuales.</strong>
                                </p>
                                <div class="redirect-notice">
                                    <i class="fas fa-info-circle text-info"></i> Serás redirigido al login después de la importación.
                                </div>
                                <form id="importForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <input type="file" name="sql_file" id="sql_file" class="form-control-file" accept=".sql,.txt" required>
                                        <small class="form-text text-muted">Selecciona un archivo SQL (máximo 10MB)</small>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <button id="importBtn" class="btn btn-database btn-import" disabled>
                                    <i class="fas fa-upload"></i> Importar SQL
                                    <div class="loading-spinner" id="importSpinner"></div>
                                </button>
                            </div>
                            <div class="col-12 mt-2">
                                <div id="importMessage" class="alert-area"></div>
                            </div>
                        </div>

                        <!-- Reset Section -->
                        <div class="row danger-zone p-3 rounded">
                            <div class="col-md-8">
                                <h5><i class="fas fa-exclamation-triangle text-danger"></i> Restablecer Base de Datos</h5>
                                <p class="feature-text text-danger">
                                    <strong>ADVERTENCIA CRÍTICA:</strong> Esta acción eliminará todos los datos y restaurará la base de datos a su estado inicial. Esta acción no se puede deshacer.
                                </p>
                                <div class="redirect-notice">
                                    <i class="fas fa-info-circle text-info"></i> Serás redirigido al login después del restablecimiento.
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <button id="resetBtn" class="btn btn-database btn-reset" data-toggle="modal" data-target="#confirmResetModal">
                                    <i class="fas fa-trash"></i> Restablecer BD
                                </button>
                            </div>
                            <div class="col-12 mt-2">
                                <div id="resetMessage" class="alert-area"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Reset Modal -->
    <div class="modal fade" id="confirmResetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Restablecimiento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h6><strong>¡ADVERTENCIA!</strong></h6>
                        <p class="mb-0">
                            Estás a punto de eliminar <strong>TODOS LOS DATOS</strong> de la base de datos. Esto incluye:
                        </p>
                        <ul class="mt-2">
                            <li>Todos los productos y categorías</li>
                            <li>Todos los usuarios excepto el administrador</li>
                            <li>Todos los registros de entradas, salidas y préstamos</li>
                            <li>Todos los proveedores y proyectos</li>
                        </ul>
                        <p class="mt-2 mb-0"><strong>Esta acción NO se puede deshacer.</strong></p>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Nota:</strong> Serás redirigido automáticamente a la página de login después de completar esta acción.
                    </div>
                    <p>Para confirmar, escribe <strong>CONFIRMAR RESET</strong> en el siguiente campo:</p>
                    <input type="text" id="confirmText" class="form-control" placeholder="Escribe CONFIRMAR RESET aquí">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="confirmResetBtn" class="btn btn-danger" disabled>
                        <i class="fas fa-trash"></i> Restablecer Base de Datos
                        <div class="loading-spinner" id="resetSpinner"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        const token = '{{ $token }}';
        const apiBackup = '{{ $apiBackup }}';
        const apiImport = '{{ $apiImport }}';
        const apiReset = '{{ $apiReset }}';
        const loginRoute = '{{ route("login") }}';

        console.log('🔐 Database Management - Sanctum Mode', {
            token: token ? `✅ Present (${token.length} chars)` : '❌ MISSING',
            apiBackup: apiBackup,
            apiImport: apiImport,
            apiReset: apiReset
        });

        // Habilitar botón de importar
        $('#sql_file').change(function() {
            $('#importBtn').prop('disabled', !$(this).val());
        });

        // Validación de confirmación
        $('#confirmText').on('input', function() {
            $('#confirmResetBtn').prop('disabled', $(this).val() !== 'CONFIRMAR RESET');
        });

        // Backup Database
        $('#backupBtn').click(function() {
            executeBackup();
        });

        // Import Database  
        $('#importBtn').click(function() {
            executeImport();
        });

        // Reset Database
        $('#confirmResetBtn').click(function() {
            executeReset();
        });

        // FUNCIONES PRINCIPALES
        function executeBackup() {
            const btn = $('#backupBtn');
            const spinner = $('#backupSpinner');
            const messageDiv = $('#backupMessage');
            
            btn.prop('disabled', true);
            spinner.show();
            messageDiv.html('');

            const downloadLink = document.createElement('a');
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);

            $.ajax({
                url: apiBackup,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/sql',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    const blob = new Blob([data], { type: 'application/sql' });
                    const url = window.URL.createObjectURL(blob);
                    
                    downloadLink.href = url;
                    downloadLink.download = 'backup_' + new Date().toISOString().replace(/[:.]/g, '-') + '.sql';
                    downloadLink.click();
                    
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(downloadLink);
                    
                    showAlert(messageDiv, '✅ Backup generado y descargado exitosamente', 'success');
                },
                error: function(xhr) {
                    // En caso de error, redirigir al login
                    showAlert(messageDiv, '❌ Error al generar backup. Redirigiendo al login...', 'danger');
                    setTimeout(() => {
                        window.location.href = loginRoute;
                    }, 2000);
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.hide();
                }
            });
        }

        function executeImport() {
            const btn = $('#importBtn');
            const spinner = $('#importSpinner');
            const messageDiv = $('#importMessage');
            const formData = new FormData($('#importForm')[0]);

            if (!formData.get('sql_file')) {
                showAlert(messageDiv, '⚠️ Por favor selecciona un archivo SQL', 'warning');
                return;
            }

            btn.prop('disabled', true);
            spinner.show();
            messageDiv.html('');

            $.ajax({
                url: apiImport,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showAlert(messageDiv, '✅ Base de datos importada correctamente. Redirigiendo al login...', 'success');
                    
                    // Redirigir al login después de 2 segundos
                    setTimeout(() => {
                        window.location.href = loginRoute;
                    }, 2000);
                },
                error: function(xhr) {
                    // En caso de error, también redirigir al login
                    showAlert(messageDiv, '❌ Error en la importación. Redirigiendo al login...', 'danger');
                    setTimeout(() => {
                        window.location.href = loginRoute;
                    }, 2000);
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.hide();
                    $('#importForm')[0].reset();
                    $('#importBtn').prop('disabled', true);
                }
            });
        }

        function executeReset() {
            const btn = $('#confirmResetBtn');
            const spinner = $('#resetSpinner');
            const messageDiv = $('#resetMessage');
            const modal = $('#confirmResetModal');

            btn.prop('disabled', true);
            spinner.show();
            messageDiv.html('');

            $.ajax({
                url: apiReset,
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: JSON.stringify({
                    confirmed_by: '{{ session("user_name") ?? "Administrador" }}'
                }),
                success: function(response) {
                    showAlert(messageDiv, '✅ Base de datos restablecida correctamente. Redirigiendo al login...', 'success');
                    modal.modal('hide');
                    
                    // Redirigir al login después de 2 segundos
                    setTimeout(() => {
                        window.location.href = loginRoute;
                    }, 2000);
                },
                error: function(xhr) {
                    // En caso de error, también redirigir al login
                    showAlert(messageDiv, '❌ Error al restablecer la base de datos. Redirigiendo al login...', 'danger');
                    setTimeout(() => {
                        window.location.href = loginRoute;
                    }, 2000);
                    btn.prop('disabled', false);
                },
                complete: function() {
                    spinner.hide();
                    modal.modal('hide');
                    $('#confirmText').val('');
                }
            });
        }

        // FUNCIONES AUXILIARES
        function showAlert(container, message, type) {
            const html = `
                <div class="alert alert-${type} alert-dismissible fade show">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            `;
            container.html(html);
        }

        $('#confirmResetModal').on('hidden.bs.modal', function() {
            $('#confirmText').val('');
            $('#confirmResetBtn').prop('disabled', true);
        });
    });
    </script>
</body>
</html>
@endsection