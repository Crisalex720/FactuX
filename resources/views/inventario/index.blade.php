@extends('layouts.app')

@section('title', 'Inventario de Productos')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center mb-4">
        <div class="col-lg-12">
            <div class="card shadow-sm border-10">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                        <h2 class="mb-0">Inventario de Productos</h2>
                        <!-- Botones de acción -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-secondary" id="abrirModalStock">
                                <i class="bi bi-box-seam"></i> Ajustar Stock
                            </button>
                            <button class="btn btn-success" id="abrirModal">
                                <i class="bi bi-plus-circle"></i> Agregar Producto
                            </button>
                        </div>
                    </div>

                    <form method="get" class="row g-6 align-items-center mb-3">
                        <div class="col-auto">
                            <input type="text" name="busqueda" value="{{ $busqueda }}" class="form-control form-control-sm" placeholder="Buscar por nombre o código...">
                        </div>
                        <div class="col-auto">
                            <label class="col-form-label">Mostrar:</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" name="limite" min="1" value="{{ $limite }}" class="form-control form-control-sm" style="width:80px;">
                        </div>
                        <div class="col-auto">
                            <span>productos</span>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary btn-sm" type="submit">Actualizar</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Código de Barras</th>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>Precio Costo</th>
                                    <th>Precio Venta</th>
                                    <th>IVA %</th>
                                    <th>Valor IVA</th>
                                    <th>Imagen</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productos as $producto)
                                    <tr>
                                        <td>{{ $producto->id_producto }}</td>
                                        <td>{{ $producto->barcode }}</td>
                                        <td>{{ $producto->nombre_prod }}</td>
                                        <td>{{ number_format($producto->cantidad_prod) }}</td>
                                        <td>${{ number_format($producto->precio_costop, 2) }}</td>
                                        <td>${{ number_format($producto->precio_ventap, 2) }}</td>
                                        <td>{{ number_format($producto->iva_porcentaje ?? 0, 2) }}%</td>
                                        <td>${{ number_format($producto->valor_iva ?? 0, 2) }}</td>
                                        <td>
                                            @if (!empty($producto->imagen_url))
                                                <button class="btn btn-info btn-sm" onclick="verImagen('{{ asset($producto->imagen_url) }}')" type="button">
                                                    <i class="bi bi-image"></i> Ver Imagen
                                                </button>
                                            @else
                                                <span class="text-muted">Sin imagen</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="editarProducto('{{ $producto->id_producto }}', '{{ $producto->barcode }}', '{{ $producto->nombre_prod }}', '{{ $producto->cantidad_prod }}', '{{ $producto->precio_costop }}', '{{ $producto->precio_ventap }}', '{{ $producto->imagen_url ?? '' }}', '{{ $producto->iva_porcentaje ?? 0 }}', '{{ $producto->valor_iva ?? 0 }}')">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </button>
                                            <form action="{{ route('inventario.destroy', $producto->id_producto) }}" method="POST" style="display: inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este producto?');">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No hay productos registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para agregar producto -->
<div id="modalAgregar" class="modal">
    <div class="modal-content">
        <span class="close" id="cerrarModal">&times;</span>
        <h3 class="mb-3">Agregar Producto</h3>
        <form method="post" action="{{ route('inventario.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Código de Barras *</label>
                <input type="text" name="codigo_barra" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre del producto *</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad de unidades *</label>
                <input type="number" name="cantidad" min="0" class="form-control" required value="0">
            </div>
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Precio Costo *</label>
                    <input type="number" name="precio_costo" min="0" step="0.01" class="form-control" required value="0.00" placeholder="$0.00">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Precio Venta *</label>
                    <input type="number" name="precio_venta" id="add_precio_venta" min="0" step="0.01" class="form-control" required placeholder="$0.00" onchange="calcularIVAAgregar()">
                </div>
            </div>
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">IVA (%) <small class="text-muted">(0 = sin IVA)</small></label>
                    <input type="number" name="iva_porcentaje" id="add_iva_porcentaje" min="0" max="100" step="0.01" class="form-control" value="0" placeholder="0.00" onchange="calcularIVAAgregar()">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Valor IVA <small class="text-muted">(calculado)</small></label>
                    <input type="number" name="valor_iva" id="add_valor_iva" min="0" step="0.01" class="form-control" value="0" readonly style="background-color: #f8f9fa;">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Imagen del producto</label>
                <input type="file" name="imagen" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-success w-100" type="submit">Registrar</button>
        </form>
    </div>
</div>

<!-- Modal para editar producto -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <span class="close" id="cerrarEditar">&times;</span>
        <h3 class="mb-3">Editar Producto</h3>
        <form id="formEditar" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Código de Barras</label>
                <input type="text" name="codigo_barra" id="edit_codigo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cantidad</label>
                <input type="number" name="cantidad" id="edit_cantidad" min="0" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Precio Costo</label>
                <input type="number" name="precio_costo" id="edit_precio_costo" min="0" step="0.01" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label class="form-label">Precio Venta</label>
                <input type="number" name="precio_venta" id="edit_precio_venta" min="0" step="0.01" class="form-control" required onchange="calcularIVAEditar()">
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">IVA (%) <small class="text-muted">(0 = sin IVA)</small></label>
                    <input type="number" name="iva_porcentaje" id="edit_iva_porcentaje" min="0" max="100" step="0.01" class="form-control" value="0" onchange="calcularIVAEditar()">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Valor IVA <small class="text-muted">(calculado)</small></label>
                    <input type="number" name="valor_iva" id="edit_valor_iva" min="0" step="0.01" class="form-control" value="0" readonly style="background-color: #f8f9fa;">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Imagen actual</label>
                <div id="edit_imagen_actual" style="margin-bottom:0.5rem;"></div>
                <button type="button" class="btn btn-outline-danger btn-sm mb-2" id="btnEliminarImagen" style="display:none;">Eliminar imagen</button>
                <input type="hidden" name="eliminar_imagen" id="eliminar_imagen" value="0">
                <label class="form-label">Cambiar imagen</label>
                <input type="file" name="imagen" class="form-control" accept="image/*">
            </div>
            <button class="btn btn-warning w-100" type="submit">Actualizar</button>
        </form>
    </div>
</div>

<!-- Modal para ajustar stock -->
<div id="modalStock" class="modal">
    <div class="modal-content">
        <span class="close" id="cerrarModalStock">&times;</span>
        <h3 class="mb-3">Ajustar Stock de Productos</h3>
        <form method="post" action="{{ route('inventario.ajustar-stock') }}">
            @csrf
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código de Barras</th>
                        <th>Nombre</th>
                        <th>Unidades Disponibles</th>
                        <th>Sumar</th>
                        <th>Restar</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($productos as $producto)
                    <tr>
                        <td>{{ $producto->id_producto }}</td>
                        <td>{{ $producto->barcode }}</td>
                        <td>{{ $producto->nombre_prod }}</td>
                        <td>{{ $producto->cantidad_prod }}</td>
                        <td><input type="number" name="sumar[{{ $producto->id_producto }}]" min="0" class="form-control form-control-sm" style="width:80px;"></td>
                        <td><input type="number" name="restar[{{ $producto->id_producto }}]" min="0" class="form-control form-control-sm" style="width:80px;"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button class="btn btn-primary w-100" type="submit">Aplicar Cambios</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Modal agregar
var modal = document.getElementById('modalAgregar');
var btn = document.getElementById('abrirModal');
var span = document.getElementById('cerrarModal');
btn.onclick = function() { modal.style.display = 'block'; }
span.onclick = function() { modal.style.display = 'none'; }
window.onclick = function(event) { if (event.target == modal) { modal.style.display = 'none'; } }

// Modal editar
var modalEditar = document.getElementById('modalEditar');
var cerrarEditar = document.getElementById('cerrarEditar');
cerrarEditar.onclick = function() { modalEditar.style.display = 'none'; }

function editarProducto(id, codigo, nombre, cantidad, precio_costo, precio_venta, imagen_url, iva_porcentaje, valor_iva) {
    document.getElementById('edit_codigo').value = codigo;
    document.getElementById('edit_nombre').value = nombre;
    // Asegurar que cantidad tenga un valor numérico válido
    document.getElementById('edit_cantidad').value = cantidad || 0;
    // Asegurar que precio_costo tenga un valor numérico válido  
    document.getElementById('edit_precio_costo').value = precio_costo || 0;
    document.getElementById('edit_precio_venta').value = precio_venta;
    // Asegurar que IVA tenga valores numéricos válidos
    document.getElementById('edit_iva_porcentaje').value = iva_porcentaje || 0;
    document.getElementById('edit_valor_iva').value = valor_iva || 0;
    
    // Configurar la acción del formulario
    document.getElementById('formEditar').action = '/inventario/' + id;
    
    // Imagen actual
    var imgDiv = document.getElementById('edit_imagen_actual');
    var btnEliminar = document.getElementById('btnEliminarImagen');
    var inputEliminar = document.getElementById('eliminar_imagen');
    
    if (imagen_url && imagen_url !== '') {
        imgDiv.innerHTML = '<img src="{{ asset("") }}' + imagen_url + '" style="max-width:120px;max-height:80px;border-radius:6px;">';
        btnEliminar.style.display = 'inline-block';
        inputEliminar.value = '0';
    } else {
        imgDiv.innerHTML = '<span class="text-muted">Sin imagen</span>';
        btnEliminar.style.display = 'none';
        inputEliminar.value = '0';
    }
    
    btnEliminar.onclick = function() {
        imgDiv.innerHTML = '<span class="text-muted">Sin imagen</span>';
        inputEliminar.value = '1';
        btnEliminar.style.display = 'none';
    };
    
    modalEditar.style.display = 'block';
}

// Modal para ver imagen
function verImagen(url) {
    var modal = document.createElement('div');
    modal.id = 'modalImagenCustom';
    modal.style.cssText = 'position:fixed;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);display:flex;align-items:center;justify-content:center;z-index:9999;';
    var img = document.createElement('img');
    img.src = url;
    img.style.cssText = 'max-width:90vw;max-height:80vh;border:8px solid #fff;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,0.18);';
    modal.appendChild(img);
    modal.onclick = function() { document.body.removeChild(modal); };
    document.body.appendChild(modal);
}

// Modal de stock
var modalStock = document.getElementById('modalStock');
var btnStock = document.getElementById('abrirModalStock');
var closeStock = document.getElementById('cerrarModalStock');
btnStock.onclick = function() { modalStock.style.display = 'block'; }
closeStock.onclick = function() { modalStock.style.display = 'none'; }

// Funciones para calcular IVA
function calcularIVAAgregar() {
    var precioVenta = parseFloat(document.getElementById('add_precio_venta').value) || 0;
    var ivaPorcentaje = parseFloat(document.getElementById('add_iva_porcentaje').value) || 0;
    var valorIva = (precioVenta * ivaPorcentaje) / 100;
    document.getElementById('add_valor_iva').value = valorIva.toFixed(2);
}

function calcularIVAEditar() {
    var precioVenta = parseFloat(document.getElementById('edit_precio_venta').value) || 0;
    var ivaPorcentaje = parseFloat(document.getElementById('edit_iva_porcentaje').value) || 0;
    var valorIva = (precioVenta * ivaPorcentaje) / 100;
    document.getElementById('edit_valor_iva').value = valorIva.toFixed(2);
}
</script>
@endsection
