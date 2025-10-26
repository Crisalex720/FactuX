<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Cierre de Caja #{{ $caja->id_caja }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        .report-info {
            font-size: 11px;
            color: #888;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table .label {
            font-weight: bold;
            background-color: #f8f9fa;
            width: 30%;
        }
        .summary-boxes {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            background-color: #f8f9fa;
            width: 25%;
        }
        .summary-box .value {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
        }
        .summary-box .label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .facturas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .facturas-table th,
        .facturas-table td {
            padding: 4px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .facturas-table th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        .facturas-table td {
            font-size: 9px;
        }
        .facturas-table tfoot th {
            background-color: #28a745;
            color: white;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        .diferencia-positiva {
            color: #28a745;
            font-weight: bold;
        }
        .diferencia-negativa {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        .observaciones {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">FactuX</div>
        <div class="report-title">Reporte de Cierre de Caja</div>
        <div class="report-info">
            Caja #{{ $caja->id_caja }} | Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Información General -->
    <div class="section">
        <div class="section-title">Información General del Cierre</div>
        <table class="info-table">
            <tr>
                <td class="label">ID de Caja:</td>
                <td>#{{ $caja->id_caja }}</td>
                <td class="label">Tipo de Cierre:</td>
                <td>{{ ucfirst($caja->tipo_cierre) }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de Apertura:</td>
                <td>{{ $caja->fecha_apertura->format('d/m/Y H:i:s') }}</td>
                <td class="label">Fecha de Cierre:</td>
                <td>{{ $caja->fecha_cierre->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <td class="label">Abierto por:</td>
                <td>{{ $caja->trabajadorApertura->nombre }} {{ $caja->trabajadorApertura->apellido }}</td>
                <td class="label">Cerrado por:</td>
                <td>{{ $caja->trabajadorCierre->nombre }} {{ $caja->trabajadorCierre->apellido }}</td>
            </tr>
            @if($caja->observaciones)
            <tr>
                <td class="label">Observaciones:</td>
                <td colspan="3" class="observaciones">{{ $caja->observaciones }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Resumen Financiero -->
    <div class="section">
        <div class="section-title">Resumen Financiero</div>
        <table class="summary-boxes">
            <tr>
                <td class="summary-box">
                    <div class="value">${{ number_format($caja->dinero_base, 0) }}</div>
                    <div class="label">Dinero Base</div>
                </td>
                <td class="summary-box">
                    <div class="value text-success">${{ number_format($caja->total_ventas, 0) }}</div>
                    <div class="label">Total Ventas</div>
                </td>
                <td class="summary-box">
                    <div class="value">${{ number_format($caja->dinero_contado, 0) }}</div>
                    <div class="label">Dinero Contado</div>
                </td>
                <td class="summary-box">
                    <div class="value {{ $caja->diferencia >= 0 ? 'diferencia-positiva' : 'diferencia-negativa' }}">
                        ${{ number_format($caja->diferencia, 0) }}
                    </div>
                    <div class="label">{{ $caja->diferencia >= 0 ? 'Sobrante' : 'Faltante' }}</div>
                </td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td class="label">Total Esperado:</td>
                <td class="text-right">${{ number_format($caja->dinero_base + $caja->total_ventas, 0) }}</td>
            </tr>
            <tr>
                <td class="label">Total Contado:</td>
                <td class="text-right">${{ number_format($caja->dinero_contado, 0) }}</td>
            </tr>
            <tr style="background-color: {{ $caja->diferencia >= 0 ? '#d4edda' : '#f8d7da' }};">
                <td class="label">Diferencia:</td>
                <td class="text-right {{ $caja->diferencia >= 0 ? 'diferencia-positiva' : 'diferencia-negativa' }}">
                    ${{ number_format($caja->diferencia, 0) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Estadísticas de Ventas -->
    <div class="section">
        <div class="section-title">Estadísticas de Ventas</div>
        <table class="info-table">
            <tr>
                <td class="label">Total de Facturas:</td>
                <td>{{ $estadisticas['total_facturas'] }}</td>
                <td class="label">Productos Vendidos:</td>
                <td>{{ number_format($estadisticas['total_productos_vendidos']) }}</td>
            </tr>
            <tr>
                <td class="label">Factura Mayor:</td>
                <td>${{ number_format($estadisticas['factura_mayor'] ?? 0, 0) }}</td>
                <td class="label">Factura Menor:</td>
                <td>${{ number_format($estadisticas['factura_menor'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td class="label">Promedio por Venta:</td>
                <td>${{ number_format($estadisticas['promedio_venta'] ?? 0, 0) }}</td>
                <td class="label">Duración del Período:</td>
                <td>{{ $caja->fecha_apertura->diffForHumans($caja->fecha_cierre) }}</td>
            </tr>
        </table>
    </div>

    <!-- Detalle de Facturas -->
    <div class="section">
        <div class="section-title">Detalle de Facturas del Período ({{ $facturas->count() }} facturas)</div>
        @if($facturas->count() > 0)
            <table class="facturas-table">
                <thead>
                    <tr>
                        <th>Consecutivo</th>
                        <th>Fecha/Hora</th>
                        <th>Cliente</th>
                        <th>Productos</th>
                        <th>Atendido por</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facturas as $factura)
                    <tr>
                        <td>{{ $factura->prefijo }}-{{ $factura->consecutivo }}</td>
                        <td>{{ \Carbon\Carbon::parse($factura->fecha_factura ?? $factura->created_at)->format('d/m H:i') }}</td>
                        <td>{{ Str::limit($factura->nombre_cliente ?? 'Consumidor Final', 20) }}</td>
                        <td>{{ Str::limit($factura->productos ?? 'N/A', 25) }}</td>
                        <td>{{ Str::limit($factura->atendido_por ?? 'N/A', 15) }}</td>
                        <td class="text-right">${{ number_format($factura->total_factura, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">TOTAL VENTAS DEL PERÍODO:</th>
                        <th class="text-right">${{ number_format($facturas->sum('total_factura'), 0) }}</th>
                    </tr>
                </tfoot>
            </table>
        @else
            <div style="text-align: center; padding: 20px; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-style: italic;">No se registraron facturas en este período</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        Reporte generado automáticamente por FactuX el {{ \Carbon\Carbon::now()->format('d/m/Y \a \l\a\s H:i') }} | 
        Usuario: {{ Auth::guard('trabajador')->user()->nombre }} {{ Auth::guard('trabajador')->user()->apellido }}
    </div>
</body>
</html>