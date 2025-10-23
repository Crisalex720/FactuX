<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 18px;
        }
        
        .info-section {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .producto-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .producto-header {
            background-color: #28a745;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .stock-info {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
        }
        
        .stock-item {
            text-align: center;
        }
        
        .stock-label {
            font-weight: bold;
            display: block;
            color: #495057;
        }
        
        .stock-value {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
        }
        
        th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge-salida {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        
        .no-movimientos {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FactuX</h1>
        <h2>{{ $titulo }}</h2>
        @if($nombreProducto)
            <p><strong>Producto:</strong> {{ $nombreProducto }}</p>
        @endif
    </div>

    <div class="info-section">
        <div class="info-row">
            <span><strong>Período:</strong> {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}</span>
            <span><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span><strong>Total de productos:</strong> {{ count($kardexData) }}</span>
            <span><strong>Usuario:</strong> Sistema FactuX</span>
        </div>
    </div>

    @foreach($kardexData as $productoId => $datos)
        @if(!$loop->first)
            <div class="page-break"></div>
        @endif
        
        <div class="producto-section">
            <div class="producto-header">
                📦 {{ $datos['producto'] }} - Precio Unitario: ${{ number_format($datos['precio_unitario'], 2) }}
            </div>
            
            <div class="stock-info">
                <div class="stock-item">
                    <span class="stock-label">Stock Inicial</span>
                    <span class="stock-value">{{ $datos['stock_inicial'] }}</span>
                </div>
                <div class="stock-item">
                    <span class="stock-label">Total Vendido</span>
                    <span class="stock-value">{{ collect($datos['movimientos'])->sum('cantidad') }}</span>
                </div>
                <div class="stock-item">
                    <span class="stock-label">Stock Final</span>
                    <span class="stock-value">{{ $datos['stock_final'] }}</span>
                </div>
                <div class="stock-item">
                    <span class="stock-label">Valor Total Vendido</span>
                    <span class="stock-value">${{ number_format(collect($datos['movimientos'])->sum('valor_total'), 2) }}</span>
                </div>
            </div>

            @if(!empty($datos['movimientos']))
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Factura</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Valor Unit.</th>
                            <th>Valor Total</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datos['movimientos'] as $movimiento)
                            <tr>
                                <td>{{ $movimiento['fecha'] }}</td>
                                <td>{{ $movimiento['factura'] }}</td>
                                <td>{{ $movimiento['cliente'] }}</td>
                                <td>
                                    <span class="badge-salida">{{ $movimiento['tipo'] }}</span>
                                </td>
                                <td>{{ $movimiento['cantidad'] }}</td>
                                <td class="text-right">${{ number_format($movimiento['valor_unitario'], 2) }}</td>
                                <td class="text-right">${{ number_format($movimiento['valor_total'], 2) }}</td>
                                <td><strong>{{ $movimiento['saldo'] }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-movimientos">
                    No hay movimientos registrados para este producto en el período seleccionado.
                </div>
            @endif
        </div>
    @endforeach

    <div class="footer">
        <p>Reporte generado por FactuX - Sistema de Facturación | Página <span class="pagenum"></span></p>
    </div>
</body>
</html>