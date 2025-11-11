<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Proveedor - Sistema de Inventario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .supplier-detail-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
        .detail-title {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        .detail-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #f8f9fa;
        }
        .section-title {
            color: #34495e;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding: 0.5rem;
            background: white;
            border-left: 4px solid #3498db;
            border-radius: 4px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .info-item {
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }
        .info-value {
            color: #34495e;
            padding: 0.5rem 0;
        }
        .badge-detail {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .badge-nacional { background: #27ae60; color: white; }
        .badge-internacional { background: #e67e22; color: white; }
        .badge-servicios { background: #9b59b6; color: white; }
        .badge-productos { background: #3498db; color: white; }
        .badge-gobierno { background: #e74c3c; color: white; }
        .document-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .document-link:hover {
            background: #2980b9;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }
        .btn-edit {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-edit:hover {
            background: linear-gradient(135deg, #2980b9, #21618c);
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .btn-back {
            background: #95a5a6;
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            font-weight: 500;
            border-radius: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-back:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-expired { background: #f8d7da; color: #721c24; }
        .status-warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    @extends('layouts.app')

    @section('title', 'Detalles del Proveedor')

    @section('content')
    <div class="container supplier-detail-container">
        <h1 class="detail-title">
            <i class="fas fa-building"></i> {{ $supplier['nombre_razon_social'] }}
        </h1>
        
        <!-- Sección: Información Básica -->
        <div class="detail-section">
            <h5 class="section-title">
                <i class="fas fa-info-circle"></i> Información Básica
            </h5>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Código de Proveedor</div>
                    <div class="info-value"><strong>{{ $supplier['codigo_proveedor'] }}</strong></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tipo de Proveedor</div>
                    <div class="info-value">
                        <span class="badge-detail badge-{{ strtolower($supplier['tipo_proveedor']) }}">
                            {{ $supplier['tipo_proveedor'] }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">RFC</div>
                    <div class="info-value">{{ $supplier['rfc_identificacion_fiscal'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha de Registro</div>
                    <div class="info-value">
                        {{ \Carbon\Carbon::parse($supplier['created_at'])->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Información de Contacto -->
        <div class="detail-section">
            <h5 class="section-title">
                <i class="fas fa-address-book"></i> Información de Contacto
            </h5>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Persona de Contacto</div>
                    <div class="info-value">{{ $supplier['persona_contacto'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Teléfono Principal</div>
                    <div class="info-value">
                        <i class="fas fa-phone"></i> {{ $supplier['telefono_principal'] }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Correo Electrónico</div>
                    <div class="info-value">
                        <i class="fas fa-envelope"></i> 
                        <a href="mailto:{{ $supplier['correo_electronico'] }}">
                            {{ $supplier['correo_electronico'] }}
                        </a>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Teléfono Secundario</div>
                    <div class="info-value">
                        @if($supplier['telefono_secundario'])
                            <i class="fas fa-phone"></i> {{ $supplier['telefono_secundario'] }}
                        @else
                            <span class="text-muted">No especificado</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Correo Secundario</div>
                    <div class="info-value">
                        @if($supplier['correo_secundario'])
                            <i class="fas fa-envelope"></i> 
                            <a href="mailto:{{ $supplier['correo_secundario'] }}">
                                {{ $supplier['correo_secundario'] }}
                            </a>
                        @else
                            <span class="text-muted">No especificado</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Horarios de Atención</div>
                    <div class="info-value">{{ $supplier['horarios_atencion'] }}</div>
                </div>
            </div>
        </div>

        <!-- Sección: Información Financiera -->
        <div class="detail-section">
            <h5 class="section-title">
                <i class="fas fa-money-bill-wave"></i> Información Financiera
            </h5>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Cuenta Bancaria</div>
                    <div class="info-value">{{ $supplier['cuenta_bancaria'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Métodos de Pago</div>
                    <div class="info-value">{{ $supplier['metodos_pago'] }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Plazos de Crédito</div>
                    <div class="info-value">{{ $supplier['plazos_credito'] }} días</div>
                </div>
            </div>
        </div>

        <!-- Sección: Información Adicional -->
        <div class="detail-section">
            <h5 class="section-title">
                <i class="fas fa-map-marker-alt"></i> Información Adicional
            </h5>
            <div class="info-grid">
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Dirección</div>
                    <div class="info-value">{{ $supplier['direccion'] }}</div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Información Comercial</div>
                    <div class="info-value">
                        @if($supplier['informacion_comercial'])
                            {{ $supplier['informacion_comercial'] }}
                        @else
                            <span class="text-muted">No hay información comercial adicional</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Documentos -->
        <div class="detail-section">
            <h5 class="section-title">
                <i class="fas fa-file-contract"></i> Documentos
            </h5>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Archivo CSF</div>
                    <div class="info-value">
                        @if($supplier['archivo_adjunto_csf'])
                            <a href="{{ asset('storage/' . $supplier['archivo_adjunto_csf']) }}" 
                               target="_blank" class="document-link">
                                <i class="fas fa-file-pdf"></i> Ver CSF
                            </a>
                            @if($supplier['fecha_vigencia_csf'])
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $vigencia = \Carbon\Carbon::parse($supplier['fecha_vigencia_csf']);
                                    $diasRestantes = $today->diffInDays($vigencia, false);
                                @endphp
                                <div class="mt-2">
                                    <small class="d-block">Vence: {{ $vigencia->format('d/m/Y') }}</small>
                                    @if($diasRestantes > 30)
                                        <span class="status-badge status-active">Vigente</span>
                                    @elseif($diasRestantes > 0)
                                        <span class="status-badge status-warning">Por vencer ({{ $diasRestantes }} días)</span>
                                    @else
                                        <span class="status-badge status-expired">Vencido</span>
                                    @endif
                                </div>
                            @endif
                        @else
                            <span class="text-muted">No hay archivo cargado</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Opinión de Cumplimiento</div>
                    <div class="info-value">
                        @if($supplier['opinion_cumplimiento'])
                            <a href="{{ asset('storage/' . $supplier['opinion_cumplimiento']) }}" 
                               target="_blank" class="document-link">
                                <i class="fas fa-file-pdf"></i> Ver Opinión
                            </a>
                            @if($supplier['fecha_vigencia_opinion'])
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $vigencia = \Carbon\Carbon::parse($supplier['fecha_vigencia_opinion']);
                                    $diasRestantes = $today->diffInDays($vigencia, false);
                                @endphp
                                <div class="mt-2">
                                    <small class="d-block">Vence: {{ $vigencia->format('d/m/Y') }}</small>
                                    @if($diasRestantes > 30)
                                        <span class="status-badge status-active">Vigente</span>
                                    @elseif($diasRestantes > 0)
                                        <span class="status-badge status-warning">Por vencer ({{ $diasRestantes }} días)</span>
                                    @else
                                        <span class="status-badge status-expired">Vencido</span>
                                    @endif
                                </div>
                            @endif
                        @else
                            <span class="text-muted">No hay archivo cargado</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="action-buttons">
            <a href="{{ route('suppliers.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
            @if (session('role') === '1' || session('role') === '0')
            <a href="{{ route('suppliers.edit', $supplier['id']) }}" class="btn-edit">
                <i class="fas fa-edit"></i> Editar Proveedor
            </a>
            @endif
        </div>
    </div>
    @endsection
</body>
</html>
