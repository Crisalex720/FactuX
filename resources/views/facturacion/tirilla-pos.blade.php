<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tirilla POS - Factura 
        @if(isset($factura))
            {{ $factura->prefijo ?? 'FACT' }}-{{ $factura->consecutivo ?? $factura->num_fact ?? 'N/A' }}
        @else
            TEST
        @endif
    </title>
    <link rel="stylesheet" href="{{ asset('css/tirilla-pos.css') }}">
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir Tirilla
        </button>
        <a href="{{ route('facturacion.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Facturación
        </a>
    </div>

    <div class="ticket">
        <!-- Header -->
        <div class="header">
            <div class="empresa-nombre">FACTUX</div>
            <div class="empresa-info">Sistema de Facturación</div>
            <div class="empresa-info">NIT: 123456789-0</div>
            <div class="empresa-info">Tel: (601) 123-4567</div>
        </div>

        <!-- Información de la Factura -->
        <div class="factura-info">
            FACTURA DE VENTA
            <br>
            @if(isset($factura))
                {{ $factura->prefijo ?? 'FACT' }}-{{ $factura->consecutivo ?? $factura->num_fact ?? $factura->id_fact ?? 'N/A' }}
            @else
                FACT-TEST
            @endif
        </div>

        <!-- Información del Cliente -->
        <div class="cliente-info">
            <div><strong>CLIENTE:</strong></div>
            <div>
                @if(isset($cliente) && $cliente && isset($cliente->nombre_cl))
                    {{ $cliente->nombre_cl }}
                @else
                    CONSUMIDOR FINAL
                @endif
            </div>
            @if(isset($cliente) && $cliente && isset($cliente->cedula) && $cliente->cedula)
            <div>CC: {{ $cliente->cedula }}</div>
            @endif
            @if(isset($cliente) && $cliente && isset($cliente->celular) && $cliente->celular)
            <div>Tel: {{ $cliente->celular }}</div>
            @endif
            @if(isset($cliente) && $cliente && isset($cliente->correo) && $cliente->correo)
            <div>Email: {{ $cliente->correo }}</div>
            @endif
        </div>

        <!-- Información de Fecha y Vendedor -->
        <div class="cliente-info">
            <div><strong>FECHA:</strong> 
                @if(isset($factura))
                    @php
                        $fecha = $factura->fecha_factura ?? $factura->created_at ?? now();
                    @endphp
                    {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y H:i:s') }}
                @else
                    {{ now()->format('d/m/Y H:i:s') }}
                @endif
            </div>
            <div><strong>VENDEDOR:</strong> 
                @if(isset($trabajador) && $trabajador)
                    {{ $trabajador->nombre ?? 'N/A' }} {{ $trabajador->apellido ?? '' }}
                @else
                    N/A
                @endif
            </div>
        </div>

        <!-- Productos -->
        <div class="productos">
            @php
                $totalFactura = 0;
                $totalItems = 0;
                $totalIVA = 0;
            @endphp
            
            <!-- Header de columnas -->
            <div class="producto-header">
                <span class="col-cant">CANT</span>
                <span class="col-precio">PRECIO</span>
                <span class="col-iva">IVA%</span>
                <span class="col-total">TOTAL</span>
            </div>
            
            @if(isset($productos) && $productos->count() > 0)
                @foreach($productos as $producto)
                    @php
                        $subtotal = $producto->subtotal ?? 0;
                        $cantidad = $producto->cantidad ?? 1;
                        $ivaPorcentaje = $producto->iva_porcentaje ?? 0;
                        $valorIvaUnitario = $producto->valor_iva ?? 0;
                        $valorIvaTotal = $valorIvaUnitario * $cantidad;
                        
                        $totalFactura += $subtotal;
                        $totalItems += $cantidad;
                        $totalIVA += $valorIvaTotal;
                    @endphp
                    <div class="producto-item">
                        <div class="producto-nombre">{{ strtoupper($producto->nombre_prod ?? 'PRODUCTO') }}</div>
                        <div class="producto-detalle">
                            <span class="col-cant">{{ $cantidad }}</span>
                            <span class="col-precio">${{ number_format($producto->precio_ventap ?? 0, 2) }}</span>
                            <span class="col-iva">{{ number_format($ivaPorcentaje, 1) }}%</span>
                            <span class="col-total">${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="producto-item">
                    <div class="producto-nombre">PRODUCTO DE PRUEBA</div>
                    <div class="producto-detalle">
                        <span class="col-cant">1</span>
                        <span class="col-precio">$1,000.00</span>
                        <span class="col-iva">0.0%</span>
                        <span class="col-total">$1,000.00</span>
                    </div>
                </div>
                @php
                    $totalFactura = 1000;
                    $totalItems = 1;
                    $totalIVA = 0;
                @endphp
            @endif
        </div>

        <!-- Totales -->
        <div class="totales">
            <div class="total-linea">
                <span>Items:</span>
                <span>{{ $totalItems }}</span>
            </div>
            <div class="total-linea">
                <span>Subtotal:</span>
                <span>${{ number_format($totalFactura, 2) }}</span>
            </div>
            <div class="total-linea">
                <span>IVA total:</span>
                <span>${{ number_format($totalIVA, 2) }}</span>
            </div>
            <div class="total-linea total-final">
                <span>TOTAL:</span>
                <span>${{ number_format($totalFactura, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-linea">Resolución DIAN No. 18764003311377</div>
            <div class="footer-linea">Del 2024-01-01 al 2024-12-31</div>
            <div class="footer-linea">Rango autorizado: FACT-1 al FACT-10000</div>
            <div class="footer-linea">Software: FactuX v1.0</div>
            <div class="footer-linea">{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</div>
            <div class="agradecimiento">¡GRACIAS POR SU COMPRA!</div>
        </div>
    </div>

    <script>
        // Auto-imprimir cuando se carga la página si viene de finalizar factura
        document.addEventListener('DOMContentLoaded', function() {
            // Si viene con parámetro de impresión automática
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === 'true') {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        });

        // Redirigir después de imprimir
        window.addEventListener('afterprint', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === 'true') {
                setTimeout(function() {
                    window.location.href = '{{ route("facturacion.index") }}';
                }, 1000);
            }
        });
    </script>
</body>
</html>