<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ubicaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        .header p {
            color: #7f8c8d;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-active {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ubicaciones</h1>
        <p>Generado el: {{ date('d/m/Y H:i:s') }}</p>
        <p>Total de ubicaciones: {{ count($locations) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Sucursal</th>
                <th>Sección</th>
                <th>Estante</th>
                <th>Sección Estante</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $location)
            <tr>
                <td>{{ $location['codigo_ubicacion'] }}</td>
                <td>{{ $location['sucursal'] }}</td>
                <td>{{ $location['seccion_sucursal'] }}</td>
                <td>{{ $location['estante'] }}</td>
                <td>{{ $location['seccion_estante'] }}</td>
                <td>{{ $location['descripcion'] ?? 'Sin descripción' }}</td>
                <td>
                    <span class="badge {{ $location['activo'] ? 'badge-active' : 'badge-inactive' }}">
                        {{ $location['activo'] ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Sistema de Gestión de Inventarios - {{ date('Y') }}
    </div>
</body>
</html>