<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos - PDF</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .page-break {
            page-break-before: always;
        }
        .small-text {
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Inventario General</h1>
        @php
            $chunks = array_chunk($products, 16);
        @endphp
        @foreach($chunks as $index => $chunk)
            @if($index > 0)
                <div class="page-break"></div>
            @endif
            <div class="table-responsive small-text">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Precio</th>
                            <th>Observaciones</th>
                            <th>Categoría</th>
                            <th>Proveedor</th>
                            <th>Ubicación</th>
                            <th>Cantidad</th>
                            <th>Imagen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chunk as $product)
                            <tr>
                                <td>{{ $product['name'] }}</td>
                                <td>{{ $product['description'] ?? 'N/A' }}</td>
                                <td>${{ number_format($product['price'], 2, '.', ',') }}</td>
                                <td>{{ $product['observations'] ?? 'N/A' }}</td>
                                <td>{{ $product['category']['name'] ?? ($product['category_id'] ? 'Categoría ID: ' . $product['category_id'] : 'N/A') }}</td>
                                <td>
                                    @if(isset($product['supplier']['nombre_razon_social']))
                                        {{ $product['supplier']['nombre_razon_social'] }}
                                    @elseif(isset($product['supplier']['company']))
                                        {{ $product['supplier']['company'] }}
                                    @elseif($product['supplier_id'])
                                        Proveedor ID: {{ $product['supplier_id'] }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $product['location'] ?? 'N/A' }}</td>
                                <td>{{ number_format($product['quantity'] ?? 0, 0, '.', ',') }}</td>
                                <td>
                                    @if(isset($product['profile_image']) && $product['profile_image'])
                                        <img src="{{ config('app.backend_api') }}/{{ $product['profile_image'] }}" alt="Imagen del producto" width="100" style="border-radius: 10px;">
                                    @else
                                        <div style="width: 100px; height: 100px; background: #f8f9fa; border: 1px dashed #dee2e6; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                            Sin Imagen
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</body>
</html>