@extends('layouts.app')

@section('title', 'Facturación')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Facturación (Carrito de Compras)</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Formulario para agregar productos -->
    <form method="post" action="{{ route('facturacion.agregar') }}" class="mb-3">
        @csrf
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Cliente</label>
                <select name="id_cliente" class="form-control" required>
                    @if($clienteDefault)
                        <option value="{{ $clienteDefault->id_cliente }}" selected>{{ $clienteDefault->nombre_cl }}</option>
                    @endif
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id_cliente }}">{{ $cliente->nombre_cl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Producto</label>
                <select name="id_producto" class="form-control" required>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id_producto }}">{{ $producto->nombre_prod }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Agregar al carrito</button>
            </div>
        </div>
    </form>

    <!-- Carrito actual -->
    <h4>Carrito actual</h4>
    <div class="mb-2">
        <span class="badge badge-info" style="font-size:1.1em;">
            Próxima factura: <strong>FACT-{{ $nextConsecutivo }}</strong>
        </span>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio unitario</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @php
                $carrito = session('carrito', []);
                $totalFactura = 0;
            @endphp
            
            @forelse($carrito as $item)
                @php
                    $producto = $productos->firstWhere('id_producto', $item['id_producto']);
                    $precio = $producto ? $producto->precio_ventap : 0;
                    $subtotal = $precio * $item['cantidad'];
                    $totalFactura += $subtotal;
                @endphp
                <tr>
                    <td>{{ $producto ? $producto->nombre_prod : 'Producto no encontrado' }}</td>
                    <td>${{ number_format($precio, 2) }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>${{ number_format($subtotal, 2) }}</td>
                    <td>
                        <a href="{{ route('facturacion.quitar', $item['id_producto']) }}" 
                           class="btn btn-danger btn-sm">Quitar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">El carrito está vacío.</td>
                </tr>
            @endforelse
            
            @if(!empty($carrito))
                <tr style="font-weight:bold;background:#f1f5fa;">
                    <td colspan="3" class="text-right">Total factura</td>
                    <td colspan="2">${{ number_format($totalFactura, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Finalizar factura -->
    <form method="post" action="{{ route('facturacion.finalizar') }}">
        @csrf
        <input type="hidden" name="id_cliente" value="{{ $clienteDefault ? $clienteDefault->id_cliente : '' }}">
        <button type="submit" class="btn btn-success" {{ empty($carrito) ? 'disabled' : '' }}>
            Finalizar y Registrar Factura
        </button>
    </form>

    <hr>

    <!-- Listado de facturas -->
    <h3>Listado de Facturas</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Prefijo</th>
                <th>Consecutivo</th>
                <th>Cliente</th>
                <th>Productos</th>
                <th>Atendido por</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facturas as $factura)
                <tr>
                    <td>{{ $factura->prefijo_fact }}</td>
                    <td>{{ $factura->num_fact }}</td>
                    <td>{{ $factura->nombre_cliente }}</td>
                    <td>{{ $factura->productos_detalle }}</td>
                    <td>{{ $factura->atendido_por }}</td>
                    <td>${{ number_format($factura->total_factura, 2) }}</td>
                    <td>{{ $factura->estado }}</td>
                    <td>
                        @if($factura->estado !== 'anulado')
                            <a href="{{ route('facturacion.anular', $factura->id_fact) }}" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Seguro que desea anular esta factura?');">
                                Anular
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
