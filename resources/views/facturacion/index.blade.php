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
    <form id="formAgregarProducto" class="mb-3">
        @csrf
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Cliente</label>
                <select id="clienteSelect" name="id_cliente" class="form-control" required>
                    @if($clienteDefault)
                        <option value="{{ $clienteDefault->id_cliente }}" selected>{{ $clienteDefault->nombre_cl }}</option>
                    @endif
                    @foreach($clientes as $cliente)
                        @if(!$clienteDefault || $cliente->id_cliente !== $clienteDefault->id_cliente)
                            <option value="{{ $cliente->id_cliente }}">{{ $cliente->nombre_cl }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Producto</label>
                <select id="productoSelect" name="id_producto" class="form-control" required>
                    @foreach($productos as $producto)
                        <option value="{{ $producto->id_producto }}">{{ $producto->nombre_prod }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" id="cantidadInput" name="cantidad" class="form-control" min="1" value="1" required>
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
            Próxima factura: <strong id="nextConsecutivo">FACT-{{ $nextConsecutivo }}</strong>
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
        <tbody id="carritoTableBody">
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
                <tr data-producto="{{ $item['id_producto'] }}">
                    <td>{{ $producto ? $producto->nombre_prod : 'Producto no encontrado' }}</td>
                    <td>${{ number_format($precio, 2) }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>${{ number_format($subtotal, 2) }}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm btn-quitar-producto" 
                                data-producto="{{ $item['id_producto'] }}">Quitar</button>
                    </td>
                </tr>
            @empty
                <tr id="carritoVacio">
                    <td colspan="5">El carrito está vacío.</td>
                </tr>
            @endforelse
            
            @if(!empty($carrito))
                <tr id="totalRow" style="font-weight:bold;background:#f1f5fa;">
                    <td colspan="3" class="text-right">Total factura</td>
                    <td colspan="2" id="totalFactura">${{ number_format($totalFactura, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Finalizar factura -->
    <form method="post" action="{{ route('facturacion.finalizar') }}">
        @csrf
        <input type="hidden" id="clienteFactura" name="id_cliente" value="{{ $clienteDefault ? $clienteDefault->id_cliente : '' }}">
        <button type="submit" id="btnFinalizar" class="btn btn-success" {{ empty($carrito) ? 'disabled' : '' }}>
            Finalizar y Registrar Factura
        </button>
    </form>

    <hr>

    <!-- Acceso al módulo de facturas -->
    <div class="text-center">
        <a href="{{ route('facturas.index') }}" class="btn btn-info btn-lg">
            <i class="fas fa-file-invoice"></i> Ver Todas las Facturas
        </a>
        <p class="text-muted mt-2">Consulta y gestiona todas las facturas registradas</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clienteDefaultId = '{{ $clienteDefault ? $clienteDefault->id_cliente : "" }}';
    
    // Manejar envío del formulario para agregar productos
    document.getElementById('formAgregarProducto').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("facturacion.agregar.ajax") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarCarrito(data.carrito);
                // Resetear formulario
                document.getElementById('cantidadInput').value = 1;
                document.getElementById('productoSelect').selectedIndex = 0;
                
                // Actualizar cliente seleccionado en el input hidden para la factura
                const clienteSeleccionado = document.getElementById('clienteSelect').value;
                document.getElementById('clienteFactura').value = clienteSeleccionado;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Manejar botones de quitar producto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-quitar-producto')) {
            const idProducto = e.target.dataset.producto;
            
            fetch(`{{ url('facturacion/quitar-ajax') }}/${idProducto}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    actualizarCarrito(data.carrito);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });

    // Función para actualizar la tabla del carrito
    function actualizarCarrito(carritoData) {
        const tbody = document.getElementById('carritoTableBody');
        const btnFinalizar = document.getElementById('btnFinalizar');
        
        // Limpiar tabla
        tbody.innerHTML = '';
        
        if (carritoData.items.length === 0) {
            tbody.innerHTML = '<tr id="carritoVacio"><td colspan="5">El carrito está vacío.</td></tr>';
            btnFinalizar.disabled = true;
        } else {
            carritoData.items.forEach(item => {
                const fila = `
                    <tr data-producto="${item.id_producto}">
                        <td>${item.nombre_prod}</td>
                        <td>$${formatNumber(item.precio)}</td>
                        <td>${item.cantidad}</td>
                        <td>$${formatNumber(item.subtotal)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm btn-quitar-producto" 
                                    data-producto="${item.id_producto}">Quitar</button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });
            
            // Agregar fila de total
            const filaTotal = `
                <tr id="totalRow" style="font-weight:bold;background:#f1f5fa;">
                    <td colspan="3" class="text-right">Total factura</td>
                    <td colspan="2" id="totalFactura">$${formatNumber(carritoData.total)}</td>
                </tr>
            `;
            tbody.innerHTML += filaTotal;
            
            btnFinalizar.disabled = false;
        }
        
        // Actualizar próximo consecutivo
        document.getElementById('nextConsecutivo').textContent = `FACT-${carritoData.nextConsecutivo}`;
    }

    // Función para formatear números
    function formatNumber(num) {
        return new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(num);
    }

    // Función para restablecer cliente final después de crear factura
    function restablecerClienteFinal() {
        const clienteSelect = document.getElementById('clienteSelect');
        const clienteFactura = document.getElementById('clienteFactura');
        
        if (clienteDefaultId) {
            clienteSelect.value = clienteDefaultId;
            clienteFactura.value = clienteDefaultId;
        }
    }

    // Actualizar cliente de factura cuando cambie la selección
    document.getElementById('clienteSelect').addEventListener('change', function() {
        document.getElementById('clienteFactura').value = this.value;
    });

    // Restablecer cliente final al cargar la página si hay un mensaje de éxito
    @if(session('success') && str_contains(session('success'), 'Factura registrada'))
        setTimeout(function() {
            restablecerClienteFinal();
        }, 100);
    @endif
});
</script>
@endsection
