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
    <style>
        /* Estilos para impresión POS */
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.2;
            margin: 0;
            padding: 5mm;
            width: 70mm;
            max-width: 70mm;
            background: white;
        }
        
        .ticket {
            width: 100%;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
        }
        
        .empresa-nombre {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .empresa-info {
            font-size: 9px;
            margin-bottom: 1px;
        }
        
        .factura-info {
            text-align: center;
            margin: 8px 0;
            font-weight: bold;
            font-size: 12px;
        }
        
        .cliente-info {
            margin: 8px 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        
        .cliente-info div {
            font-size: 10px;
            margin-bottom: 1px;
        }
        
        .productos {
            margin: 8px 0;
        }
        
        .producto-item {
            margin-bottom: 4px;
            font-size: 10px;
        }
        
        .producto-nombre {
            font-weight: bold;
            margin-bottom: 2px;
            text-align: left;
        }
        
        .producto-header {
            display: flex;
            justify-content: space-between;
            font-family: 'Courier New', monospace;
            font-size: 9px;
            font-weight: bold;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }
        
        .producto-detalle {
            display: flex;
            justify-content: space-between;
            font-family: 'Courier New', monospace;
            font-size: 9px;
        }
        
        .col-cant {
            width: 15%;
            text-align: center;
        }
        
        .col-precio {
            width: 35%;
            text-align: right;
        }
        
        .col-total {
            width: 35%;
            text-align: right;
        }
        
        .totales {
            border-top: 1px dashed #000;
            margin-top: 8px;
            padding-top: 5px;
        }
        
        .total-linea {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 10px;
        }
        
        .total-final {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 3px;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 9px;
        }
        
        .footer-linea {
            margin-bottom: 2px;
        }
        
        .agradecimiento {
            font-weight: bold;
            margin-top: 5px;
        }
        
        /* Estilos para pantalla */
        @media screen {
            body {
                background: #f5f5f5;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: flex-start;
                min-height: 100vh;
            }
            
            .ticket {
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                padding: 10px;
                border-radius: 5px;
            }
            
            .no-print {
                text-align: center;
                margin-bottom: 20px;
            }
            
            .btn {
                padding: 10px 20px;
                margin: 0 5px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
            }
            
            .btn-primary {
                background: #007bff;
                color: white;
            }
            
            .btn-secondary {
                background: #6c757d;
                color: white;
            }
            
            .btn:hover {
                opacity: 0.8;
            }
        }
        
        /* Ocultar botones en impresión */
        @media print {
            .no-print {
                display: none !important;
            }
        }
        
        /* Estilos específicos para impresoras de 58mm */
        @media print and (max-width: 60mm) {
            body {
                width: 55mm;
                max-width: 55mm;
                font-size: 10px;
            }
            
            .empresa-nombre {
                font-size: 12px;
            }
            
            .factura-info {
                font-size: 11px;
            }
        }
    </style>
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
            @endphp
            
            <!-- Header de columnas -->
            <div class="producto-header">
                <span class="col-cant">CANT</span>
                <span class="col-precio">PRECIO UNIT</span>
                <span class="col-total">TOTAL</span>
            </div>
            
            @if(isset($productos) && $productos->count() > 0)
                @foreach($productos as $producto)
                    @php
                        $totalFactura += $producto->subtotal ?? 0;
                        $totalItems += $producto->cantidad ?? 0;
                    @endphp
                    <div class="producto-item">
                        <div class="producto-nombre">{{ strtoupper($producto->nombre_prod ?? 'PRODUCTO') }}</div>
                        <div class="producto-detalle">
                            <span class="col-cant">{{ $producto->cantidad ?? 1 }}</span>
                            <span class="col-precio">${{ number_format($producto->precio_ventap ?? 0, 0) }}</span>
                            <span class="col-total">${{ number_format($producto->subtotal ?? 0, 0) }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="producto-item">
                    <div class="producto-nombre">PRODUCTO DE PRUEBA</div>
                    <div class="producto-detalle">
                        <span class="col-cant">1</span>
                        <span class="col-precio">$1,000</span>
                        <span class="col-total">$1,000</span>
                    </div>
                </div>
                @php
                    $totalFactura = 1000;
                    $totalItems = 1;
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
                <span>${{ number_format($totalFactura, 0) }}</span>
            </div>
            <div class="total-linea">
                <span>IVA (0%):</span>
                <span>$0</span>
            </div>
            <div class="total-linea total-final">
                <span>TOTAL:</span>
                <span>${{ number_format($totalFactura, 0) }}</span>
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