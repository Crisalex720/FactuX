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
        
        <!-- Fila 1: Cliente y Código de Barras -->
        <div class="row align-items-end mb-2">
            <div class="col-md-5">
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
            <div class="col-md-5">
                <label class="form-label">
                    <i class="fas fa-barcode"></i> Código de Barras
                </label>
                <input type="text" id="codigoBarrasInput" name="codigo_barras" class="form-control" 
                       placeholder="Escanea o escribe el código de barras" autocomplete="off">
            </div>
            <div class="col-md-2">
                <button type="button" id="btnLimpiarBarras" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>
        
        <!-- Fila 2: Producto, Cantidad y Agregar -->
        <div class="row align-items-end">
            <div class="col-md-5">
                <label class="form-label">Producto</label>
                <select id="productoSelect" name="id_producto" class="form-control" required>
                    <option value="">Seleccionar producto...</option>
                    @foreach($productos->take(10) as $producto)
                        <option value="{{ $producto->id_producto }}" data-codigo="{{ $producto->codigo_prod ?? '' }}">
                            {{ $producto->nombre_prod }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Buscar Producto</label>
                <input type="text" id="buscarProductoInput" class="form-control" 
                       placeholder="Escribir para buscar..." autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" id="cantidadInput" name="cantidad" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-cart-plus"></i> Agregar
                </button>
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
    const todosProductos = @json($productos);
    
    // Manejar código de barras
    const codigoBarrasInput = document.getElementById('codigoBarrasInput');
    const productoSelect = document.getElementById('productoSelect');
    const cantidadInput = document.getElementById('cantidadInput');
    const buscarProductoInput = document.getElementById('buscarProductoInput');
    
    // Función para buscar producto por código de barras
    codigoBarrasInput.addEventListener('input', function() {
        const codigo = this.value.trim();
        if (codigo.length > 0) {
            const producto = todosProductos.find(p => p.codigo_prod === codigo);
            if (producto) {
                productoSelect.value = producto.id_producto;
                cantidadInput.focus();
                cantidadInput.select();
            }
        }
    });
    
    // Permitir agregar con Enter en código de barras
    codigoBarrasInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const codigo = this.value.trim();
            if (codigo.length > 0) {
                const producto = todosProductos.find(p => p.codigo_prod === codigo);
                if (producto) {
                    productoSelect.value = producto.id_producto;
                    document.getElementById('formAgregarProducto').dispatchEvent(new Event('submit'));
                }
            }
        }
    });
    
    // Limpiar código de barras
    document.getElementById('btnLimpiarBarras').addEventListener('click', function() {
        codigoBarrasInput.value = '';
        codigoBarrasInput.focus();
    });
    
    // Búsqueda de productos
    buscarProductoInput.addEventListener('input', function() {
        const termino = this.value.toLowerCase().trim();
        const selectProducto = document.getElementById('productoSelect');
        
        // Limpiar opciones actuales
        selectProducto.innerHTML = '<option value="">Seleccionar producto...</option>';
        
        if (termino.length === 0) {
            // Mostrar solo los primeros 10 productos
            todosProductos.slice(0, 10).forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.id_producto;
                option.textContent = producto.nombre_prod;
                option.setAttribute('data-codigo', producto.codigo_prod || '');
                selectProducto.appendChild(option);
            });
        } else {
            // Filtrar productos por nombre
            const productosFiltrados = todosProductos.filter(producto => 
                producto.nombre_prod.toLowerCase().includes(termino)
            ).slice(0, 15); // Máximo 15 resultados
            
            productosFiltrados.forEach(producto => {
                const option = document.createElement('option');
                option.value = producto.id_producto;
                option.textContent = producto.nombre_prod;
                option.setAttribute('data-codigo', producto.codigo_prod || '');
                selectProducto.appendChild(option);
            });
            
            if (productosFiltrados.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No se encontraron productos';
                option.disabled = true;
                selectProducto.appendChild(option);
            }
        }
    });
    
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
                document.getElementById('codigoBarrasInput').value = '';
                document.getElementById('buscarProductoInput').value = '';
                document.getElementById('productoSelect').selectedIndex = 0;
                
                // Recargar productos iniciales
                buscarProductoInput.dispatchEvent(new Event('input'));
                
                // Enfocar código de barras para siguiente producto
                codigoBarrasInput.focus();
                
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
