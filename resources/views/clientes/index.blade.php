@extends('layouts.app')

@section('title', 'Gestión de Clientes')

@push('styles')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/usuarios.css') }}" rel="stylesheet">
<style>
.clientes-container {
    max-width: 1200px;
    margin: 0 auto;
}

.clientes-title {
    color: #007bff;
    font-weight: 600;
    margin-bottom: 2rem;
}

.clientes-form-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.clientes-table-card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: none;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
}

.fila-campos {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.fila-campos > div {
    flex: 1;
}

.fila-selects {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.fila-selects > div {
    flex: 1;
}

.fila-boton {
    text-align: center;
    margin-top: 20px;
}
</style>
@endpush

<head>
    @stack('styles')
</head>

@section('content')
<div class="clientes-container container py-4">
    <h2 class="clientes-title text-center mb-4">Gestión de Clientes</h2>
    
    @if(session('success') || session('error'))
        <div id="modalMensaje" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="cerrarModalMensaje">&times;</span>
                <div id="contenidoMensaje" class="{{ session('success') ? 'text-success' : 'text-danger' }}">
                    {{ session('success') ?? session('error') }}
                </div>
            </div>
        </div>
    @endif
    
    <div class="card clientes-form-card mb-4">
        <div class="card-body">
            <form method="post" action="{{ route('clientes.store') }}">
                @csrf
                <div class="fila-campos text-center">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label class="mb-1">Cédula</label>
                            <input type="number" name="cedula" class="form-control" required value="{{ old('cedula') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="mb-1">Nombre Completo</label>
                            <input type="text" name="nombre_cl" class="form-control" required value="{{ old('nombre_cl') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="mb-1">Celular</label>
                            <input type="number" name="celular" class="form-control" value="{{ old('celular') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" class="form-control" value="{{ old('correo') }}">
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="row g-4 align-items-end text-center">
                        <div class="col-md-3">
                            <label class="mb-1">País</label>
                            <select name="id_pais" class="form-control form-control-sm" required>
                                <option value="">Seleccionar País</option>
                                @foreach($paises as $pais)
                                    <option value="{{ $pais->id_pais }}" {{ old('id_pais') == $pais->id_pais ? 'selected' : '' }}>{{ $pais->nombre_pais }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="mb-1">Departamento</label>
                            <select name="id_depart" class="form-control form-control-sm" required>
                                <option value="">Seleccionar Depto</option>
                                @foreach($departamentos as $departamento)
                                    <option value="{{ $departamento->id_depart }}" {{ old('id_depart') == $departamento->id_depart ? 'selected' : '' }}>{{ $departamento->nombre_depart }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="mb-1">Ciudad</label>
                            <select name="id_ciudad" class="form-control form-control-sm" required>
                                <option value="">Seleccionar Ciudad</option>
                                @foreach($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id_ciudad }}" data-depart="{{ $ciudad->id_depart }}" {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>{{ $ciudad->nombre_ciudad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-1">
                                <button type="submit" name="crear" class="btn btn-success btn-sm">Registrar Cliente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card clientes-table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Celular</th>
                            <th>Correo</th>
                            <th>País</th>
                            <th>Depto</th>
                            <th>Ciudad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->id_cliente }}</td>
                                <td>{{ $cliente->cedula }}</td>
                                <td>{{ $cliente->nombre_cl }}</td>
                                <td>{{ $cliente->celular ?? 'N/A' }}</td>
                                <td>{{ $cliente->correo ?? 'N/A' }}</td>
                                <td>{{ $cliente->pais->nombre_pais ?? 'N/A' }}</td>
                                <td>{{ $cliente->departamento->nombre_depart ?? 'N/A' }}</td>
                                <td>{{ $cliente->ciudad->nombre_ciudad ?? 'N/A' }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="editarCliente(
                                        '{{ $cliente->id_cliente }}',
                                        '{{ $cliente->cedula }}',
                                        '{{ $cliente->nombre_cl }}',
                                        '{{ $cliente->celular }}',
                                        '{{ $cliente->correo }}',
                                        '{{ $cliente->id_pais }}',
                                        '{{ $cliente->id_depart }}',
                                        '{{ $cliente->id_ciudad }}'
                                    )">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </button>
                                    <form action="{{ route('clientes.destroy', $cliente->id_cliente) }}" method="POST" style="display: inline; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar cliente? Esta acción no se puede deshacer.');">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No hay clientes registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modal para editar cliente -->
            <div id="modalEditarCliente" class="modal">
                <div class="modal-content">
                    <span class="close" id="cerrarModalEditarCliente">&times;</span>
                    <h3 class="mb-3">Editar Cliente</h3>
                    <form method="post" id="formEditarCliente">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_cliente" id="edit_id_cliente">
                        <div class="fila-campos">
                            <div>
                                <label>Cédula</label>
                                <input type="number" name="cedula" id="edit_cedula" class="form-control" placeholder="Cédula" required>
                            </div>
                            <div>
                                <label>Nombre Completo</label>
                                <input type="text" name="nombre_cl" id="edit_nombre_cl" class="form-control" placeholder="Nombre Completo" required>
                            </div>
                        </div>
                        <div class="fila-campos">
                            <div>
                                <label>Celular</label>
                                <input type="number" name="celular" id="edit_celular" class="form-control" placeholder="Celular">
                            </div>
                            <div>
                                <label>Correo Electrónico</label>
                                <input type="email" name="correo" id="edit_correo" class="form-control" placeholder="Correo Electrónico">
                            </div>
                        </div>
                        <div class="fila-selects">
                            <div>
                                <label>País</label>
                                <select name="id_pais" id="edit_id_pais" class="form-select" required>
                                    <option value="">País</option>
                                    @foreach($paises as $pais)
                                        <option value="{{ $pais->id_pais }}">{{ $pais->nombre_pais }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Departamento</label>
                                <select name="id_depart" id="edit_id_depart" class="form-select" required>
                                    <option value="">Depto</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->id_depart }}">{{ $departamento->nombre_depart }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Ciudad</label>
                                <select name="id_ciudad" id="edit_id_ciudad" class="form-select" required>
                                    <option value="">Ciudad</option>
                                    @foreach($ciudades as $ciudad)
                                        <option value="{{ $ciudad->id_ciudad }}" data-depart="{{ $ciudad->id_depart }}">{{ $ciudad->nombre_ciudad }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="fila-boton">
                            <button type="submit" name="editar" class="btn btn-warning w-100">Actualizar Cliente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Mostrar modal de mensaje si existe
window.onload = function() {
    var modal = document.getElementById('modalMensaje');
    var closeBtn = document.getElementById('cerrarModalMensaje');
    if (modal) {
        modal.style.display = 'block';
        closeBtn.onclick = function() { 
            modal.style.display = 'none'; 
            window.history.replaceState(null, '', window.location.pathname); 
        };
        window.onclick = function(event) { 
            if (event.target == modal) { 
                modal.style.display = 'none'; 
                window.history.replaceState(null, '', window.location.pathname); 
            } 
        }
    }
}

// Filtrar ciudades por departamento en formulario principal
const selectDepto = document.querySelector('select[name="id_depart"]');
const selectCiudad = document.querySelector('select[name="id_ciudad"]');

// Guardar todas las ciudades en JS
const ciudadesData = [
@foreach($ciudades as $ciudad)
    {id: {{ $ciudad->id_ciudad }}, nombre: '{{ addslashes($ciudad->nombre_ciudad) }}', id_depart: {{ $ciudad->id_depart }}},
@endforeach
];

// Bloquear ciudad inicialmente
selectCiudad.disabled = true;

// Al cambiar departamento, filtrar ciudades
selectDepto.addEventListener('change', function() {
    const idDept = this.value;
    selectCiudad.innerHTML = '<option value="">Seleccionar Ciudad</option>';
    if (!idDept) {
        selectCiudad.disabled = true;
        return;
    }
    const filtradas = ciudadesData.filter(c => c.id_depart == idDept);
    filtradas.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.nombre;
        selectCiudad.appendChild(opt);
    });
    selectCiudad.disabled = false;
});

// Modal editar cliente
var modalEditarCliente = document.getElementById('modalEditarCliente');
var cerrarModalEditarCliente = document.getElementById('cerrarModalEditarCliente');

cerrarModalEditarCliente.onclick = function() { 
    modalEditarCliente.style.display = 'none'; 
}

window.onclick = function(event) {
    if (event.target == modalEditarCliente) { 
        modalEditarCliente.style.display = 'none'; 
    }
}

function editarCliente(id, cedula, nombre_cl, celular, correo, id_pais, id_depart, id_ciudad) {
    document.getElementById('edit_cedula').value = cedula;
    document.getElementById('edit_nombre_cl').value = nombre_cl;
    document.getElementById('edit_celular').value = celular !== 'null' && celular ? celular : '';
    document.getElementById('edit_correo').value = correo !== 'null' && correo ? correo : '';
    document.getElementById('edit_id_pais').value = id_pais;
    document.getElementById('edit_id_depart').value = id_depart;
    
    // Configurar acción del formulario usando la ruta correcta de Laravel
    document.getElementById('formEditarCliente').action = '/clientes/' + id;
    
    // Filtrar ciudades según departamento seleccionado
    const selectCiudadEdit = document.getElementById('edit_id_ciudad');
    const opciones = selectCiudadEdit.querySelectorAll('option');
    
    opciones.forEach(opt => {
        if (opt.value === '') {
            opt.style.display = '';
        } else {
            const departId = opt.getAttribute('data-depart');
            opt.style.display = departId == id_depart ? '' : 'none';
        }
    });
    
    selectCiudadEdit.value = id_ciudad;
    
    modalEditarCliente.style.display = 'block';
}

// Al cambiar departamento en modal editar, filtrar ciudades
const selectEditDept = document.getElementById('edit_id_depart');
selectEditDept.addEventListener('change', function() {
    const idDept = this.value;
    const selectCiudadEdit = document.getElementById('edit_id_ciudad');
    const opciones = selectCiudadEdit.querySelectorAll('option');
    
    selectCiudadEdit.value = '';
    
    opciones.forEach(opt => {
        if (opt.value === '') {
            opt.style.display = '';
        } else {
            const departId = opt.getAttribute('data-depart');
            opt.style.display = departId == idDept ? '' : 'none';
        }
    });
});
</script>
<!-- Bootstrap JS, Popper.js y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection