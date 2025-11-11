<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Proveedores - PDF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-nacional { background: #27ae60; color: white; }
        .badge-internacional { background: #e67e22; color: white; }
        .badge-servicios { background: #9b59b6; color: white; }
        .badge-productos { background: #3498db; color: white; }
        .badge-gobierno { background: #e74c3c; color: white; }
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lista de Proveedores</h1>
        <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    @php
        $chunks = array_chunk($suppliers, 20);
    @endphp
    
    @foreach($chunks as $index => $chunk)
        @if($index > 0)
            <div class="page-break"></div>
        @endif
        
        <table class="table">
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
                </tr>
            </thead>
            <tbody>
                @foreach($chunk as $supplier)
                    <tr>
                        <td>{{ $supplier['codigo_proveedor'] }}</td>
                        <td>{{ $supplier['nombre_razon_social'] }}</td>
                        <td>{{ $supplier['rfc_identificacion_fiscal'] }}</td>
                        <td>
                            <span class="badge badge-{{ strtolower($supplier['tipo_proveedor']) }}">
                                {{ $supplier['tipo_proveedor'] }}
                            </span>
                        </td>
                        <td>{{ $supplier['persona_contacto'] }}</td>
                        <td>{{ $supplier['telefono_principal'] }}</td>
                        <td>{{ $supplier['correo_electronico'] }}</td>
                        <td>{{ $supplier['plazos_credito'] }} días</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($index == count($chunks) - 1)
            <div class="footer">
                <p>Total de proveedores: {{ count($suppliers) }}</p>
                <p>Sistema de Inventario - {{ config('app.name', 'Laravel') }}</p>
            </div>
        @endif
    @endforeach
</body>
</html>