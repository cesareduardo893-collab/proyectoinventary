@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I-DEB ERP - Inicio</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 0;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        .user-info {
            text-align: right;
            font-size: 14px;
        }
        
        .welcome-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .welcome-section h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #000000ff;
        }
        
        .highlight-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .highlight-section h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .highlight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .highlight-card {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 4px solid #000000ff;
        }
        
        .highlight-card h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .highlight-value {
            font-weight: bold;
            color: #e74c3c;
        }
        
        footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #7f8c8d;
        }
    </style>

        
        <section class="welcome-section">
            <h1>Bienvenido</h1>
            <p>a la página de inicio de la aplicación de inventario.</p>
            <p><strong>Email:</strong> {{ session('email', 'No se pudo obtener el email del usuario.') }}</p>
        </section>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Número de productos</h3>
                <div class="stat-value" id="products-count">0</div>
            </div>
            <div class="stat-card">
                <h3>Número de entradas</h3>
                <div class="stat-value" id="entrance-count">0</div>
            </div>
            <div class="stat-card">
                <h3>Número de salidas</h3>
                <div class="stat-value" id="out-count">0</div>
            </div>
            <div class="stat-card">
                <h3>Préstamos activos</h3>
                <div class="stat-value" id="loans-active">0</div>
            </div>
            <div class="stat-card">
                <h3>Préstamos finalizados</h3>
                <div class="stat-value" id="loans-finished">0</div>
            </div>
            <div class="stat-card">
                <h3>Total de préstamos</h3>
                <div class="stat-value" id="loans-total">0</div>
            </div>
        </div>
        
        <section class="highlight-section">
            <h2>Productos Destacados</h2>
            <div class="highlight-grid">
                <div class="highlight-card">
                    <h3>Producto con más entradas</h3>
                    <p id="product-entrance">Ninguno <span class="highlight-value">cantidad: 0</span></p>
                </div>
                <div class="highlight-card">
                    <h3>Producto con más salidas</h3>
                    <p id="product-out">Ninguno <span class="highlight-value">cantidad: 0</span></p>
                </div>
                <div class="highlight-card">
                    <h3>Producto con más préstamos</h3>
                    <p id="product-loan">Ninguno <span class="highlight-value">cantidad: 0</span></p>
                </div>
            </div>
        </section>
        
        <footer>
            <p>Sistema de Inventario I-DEB ERP &copy; {{ date('Y') }}</p>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Mostrar estado de carga
        $('.stat-value').html('<span class="loading">Cargando...</span>');
        $('#product-entrance, #product-out, #product-loan').html('Cargando... <span class="highlight-value">cantidad: ...</span>');
        
        $.ajax({
            url: '{{ route('start.getData') }}',
            method: 'GET',
            success: function(data) {
                // Actualizar estadísticas
                $('#products-count').text(data.products.count || '0');
                $('#entrance-count').text(data.entrance.count || '0');
                $('#out-count').text(data.out.count || '0');
                $('#loans-active').text(data.counts.count || '0');
                $('#loans-finished').text(data.countsFinished.count || '0');
                $('#loans-total').text(data.countsAll.count || '0');
                
                // Actualizar productos destacados
                if (data.countsProductEntrance.name) {
                    $('#product-entrance').html(`${data.countsProductEntrance.name} <span class="highlight-value">cantidad: ${data.countsProductEntrance.total_quantity || '0'}</span>`);
                } else {
                    $('#product-entrance').html('Ninguno <span class="highlight-value">cantidad: 0</span>');
                }
                
                if (data.countsProductOut.name) {
                    $('#product-out').html(`${data.countsProductOut.name} <span class="highlight-value">cantidad: ${data.countsProductOut.total_quantity || '0'}</span>`);
                } else {
                    $('#product-out').html('Ninguno <span class="highlight-value">cantidad: 0</span>');
                }
                
                if (data.countsProductLoan.name) {
                    $('#product-loan').html(`${data.countsProductLoan.name} <span class="highlight-value">cantidad: ${data.countsProductLoan.total_quantity || '0'}</span>`);
                } else {
                    $('#product-loan').html('Ninguno <span class="highlight-value">cantidad: 0</span>');
                }
            },
            error: function() {
                // Manejar errores
                $('.stat-value').text('Error');
                $('#product-entrance, #product-out, #product-loan').html('Error al cargar datos <span class="highlight-value">cantidad: N/A</span>');
            }
        });
    });
    </script>
</body>
</html>
@endsection