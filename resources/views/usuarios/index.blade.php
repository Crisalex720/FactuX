@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@push('styles')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/usuarios.css') }}" rel="stylesheet">
<style>
.password-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-toggle-btn {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    padding: 2px;
    z-index: 10;
}

.password-toggle-btn:hover {
    background-color: #f8f9fa;
}

.password-input-container input {
    padding-right: 35px;
}
</style>
@endpush

<head>
    @stack('styles')
</head>

@section('content')
<div class="usuarios-container container py-4">
    <h2 class="usuarios-title text-center mb-4">Gestión de Usuarios</h2>
    
    @if(session('success') || session('error'))
        <div id="modalMensaje" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" id="cerrarModalMensaje">&times;</span>
                <div id="contenidoMensaje">{{ session('success') ?? session('error') }}</div>
            </div>
        </div>
    @endif
    <div class="card usuarios-form-card mb-4">
        <div class="card-body">
            <form method="post" action="{{ route('usuarios.store') }}">
                @csrf
                <div class="fila-campos text-center">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="mb-1">Cédula</label>
                        <input type="number" name="cedula" class="form-control " required value="{{ old('cedula') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Apellido</label>
                        <input type="text" name="apellido" class="form-control" required value="{{ old('apellido') }}">
                    </div>
                </div>
                    <div class="row g-2 align-items-end ">
                        <div class="col-md-3">
                            <label class="mb-1">Cargo</label>
                            <input type="text" name="cargo" class="form-control form-control-sm" required value="{{ old('cargo') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="d-flex align-items-center justify-content-between mb-1">
                                <span>Contraseña</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1" onclick="toggleRegPassword()">
                                    <span id="reg_eye_icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.5 12s4-7.5 10.5-7.5S22.5 12 22.5 12s-4 7.5-10.5 7.5S1.5 12 1.5 12z"/><circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2"/></svg>
                                    </span>
                                </button>
                            </label>
                            <input type="password" name="contrasena" id="reg_contrasena" class="form-control form-control-sm" required>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="row g-4 align-items-end text-center">
                    <div class= "col-md-3">
                        <label class="mb-1">País</label>
                        <select name="id_pais" class="form-control form-control-sm" required>
                            <option value="">País</option>
                            @foreach($paises as $pais)
                                <option value="{{ $pais->id_pais }}" {{ old('id_pais') == $pais->id_pais ? 'selected' : '' }}>{{ $pais->nombre_pais }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Depto</label>
                        <select name="id_depart" class="form-control form-control-sm" required>
                            <option value="">Depto</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento->id_depart }}" {{ old('id_depart') == $departamento->id_depart ? 'selected' : '' }}>{{ $departamento->nombre_depart }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="mb-1">Ciudad</label>
                        <select name="id_ciudad" class="form-control form-control-sm" required>
                            <option value="">Ciudad</option>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}" data-depart="{{ $ciudad->id_depart }}" {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>{{ $ciudad->nombre_ciudad }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-1">
                            <button type="submit" name="crear" class="btn btn-success btn-sm">Registrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card usuarios-table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Cargo</th>
                            <th>País</th>
                            <th>Depto</th>
                            <th>Ciudad</th>
                            <th>Contraseña</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id_trab }}</td>
                                <td>{{ $usuario->cedula }}</td>
                                <td>{{ $usuario->nombre }}</td>
                                <td>{{ $usuario->apellido }}</td>
                                <td>{{ $usuario->cargo }}</td>
                                <td>{{ $usuario->pais->nombre_pais ?? 'N/A' }}</td>
                                <td>{{ $usuario->departamento->nombre_depart ?? 'N/A' }}</td>
                                <td>{{ $usuario->ciudad->nombre_ciudad ?? 'N/A' }}</td>
                                <td class="password-cell">
                                    <div class="d-flex align-items-center">
                                        <span class="password-mask me-2" id="pw_mask_{{ $usuario->id_trab }}">••••••••</span>
                                        <span class="d-none me-2" id="pw_real_{{ $usuario->id_trab }}">{{ $usuario->contraseña }}</span>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1" onclick="togglePassword({{ $usuario->id_trab }})">
                                            <span id="eye_icon_{{ $usuario->id_trab }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.5 12s4-7.5 10.5-7.5S22.5 12 22.5 12s-4 7.5-10.5 7.5S1.5 12 1.5 12z"/><circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2"/></svg>
                                            </span>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="editarUsuario(
                                        '{{ $usuario->id_trab }}',
                                        '{{ $usuario->cedula }}',
                                        '{{ $usuario->nombre }}',
                                        '{{ $usuario->apellido }}',
                                        '{{ $usuario->cargo }}',
                                        '{{ $usuario->contraseña }}',
                                        '{{ $usuario->id_pais }}',
                                        '{{ $usuario->id_depart }}',
                                        '{{ $usuario->id_ciudad }}'
                                    )">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </button>
                                    <form action="{{ route('usuarios.destroy', $usuario->id_trab) }}" method="POST" style="display: inline; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?');">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">No hay usuarios registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Modal para editar usuario -->
            <div id="modalEditarUsuario" class="modal">
                <div class="modal-content">
                    <span class="close" id="cerrarModalEditarUsuario">&times;</span>
                    <h3 class="mb-3">Editar Usuario</h3>
                    <form method="post" class="usuarios-form-row" id="formEditarUsuario">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id_trab" id="edit_id_trab">
                        <div class="fila-campos">
                            <div><input type="number" name="cedula" id="edit_cedula" class="form-control" placeholder="Cédula" required></div>
                            <div><input type="text" name="nombre" id="edit_nombre" class="form-control" placeholder="Nombre" required></div>
                            <div><input type="text" name="apellido" id="edit_apellido" class="form-control" placeholder="Apellido" required></div>
                        </div>
                        <div class="fila-campos">
                            <div><input type="text" name="cargo" id="edit_cargo" class="form-control" placeholder="Cargo" required></div>
                            <div class="password-input-container">
                                <input type="password" name="contrasena" id="edit_contrasena" class="form-control" placeholder="Contraseña" required>
                                <button type="button" class="password-toggle-btn" onclick="toggleEditPassword()">
                                    <span id="edit_eye_icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.5 12s4-7.5 10.5-7.5S22.5 12 22.5 12s-4 7.5-10.5 7.5S1.5 12 1.5 12z"/><circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2"/></svg>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="fila-selects">
                            <div>
                                <select name="id_pais" id="edit_id_pais" class="form-select" required>
                                    <option value="">País</option>
                                    @foreach($paises as $pais)
                                        <option value="{{ $pais->id_pais }}">{{ $pais->nombre_pais }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="id_depart" id="edit_id_depart" class="form-select" required>
                                    <option value="">Depto</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->id_depart }}">{{ $departamento->nombre_depart }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="id_ciudad" id="edit_id_ciudad" class="form-select" required>
                                    <option value="">Ciudad</option>
                                    @foreach($ciudades as $ciudad)
                                        <option value="{{ $ciudad->id_ciudad }}" data-depart="{{ $ciudad->id_depart }}">{{ $ciudad->nombre_ciudad }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="fila-boton">
                            <button type="submit" name="editar" class="btn btn-warning w-100">Actualizar</button>
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
// SVGs para ojo y ojo tachado
const svgEye = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1.5 12s4-7.5 10.5-7.5S22.5 12 22.5 12s-4 7.5-10.5 7.5S1.5 12 1.5 12z"/><circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2"/></svg>';
const svgEyeOff = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M1.5 12s4-7.5 10.5-7.5c2.1 0 4.1.5 5.9 1.4M22.5 12s-4 7.5-10.5 7.5c-2.1 0-4.1-.5-5.9-1.4"/><circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.5 9.5l5 5"/></svg>';

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

// Guardar todas las ciudades en JS como en el PHP original
const ciudadesData = [
@foreach($ciudades as $ciudad)
    {id: {{ $ciudad->id_ciudad }}, nombre: '{{ addslashes($ciudad->nombre_ciudad) }}', id_depart: {{ $ciudad->id_depart }}},
@endforeach
];

// Bloquear ciudad inicialmente
selectCiudad.disabled = true;

// Al cambiar departamento, filtrar ciudades (igual al PHP original)
selectDepto.addEventListener('change', function() {
    const idDept = this.value;
    selectCiudad.innerHTML = '<option value="">Ciudad</option>';
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

// Mostrar/ocultar contraseña en tabla
function togglePassword(id) {
    var mask = document.getElementById('pw_mask_' + id);
    var real = document.getElementById('pw_real_' + id);
    var icon = document.getElementById('eye_icon_' + id);
    
    if (mask.classList.contains('d-none')) {
        mask.classList.remove('d-none');
        real.classList.add('d-none');
        if (icon) icon.innerHTML = svgEye;
    } else {
        mask.classList.add('d-none');
        real.classList.remove('d-none');
        if (icon) icon.innerHTML = svgEyeOff;
    }
}

// Mostrar/ocultar contraseña en formulario de registro
function toggleRegPassword() {
    var input = document.getElementById('reg_contrasena');
    var icon = document.getElementById('reg_eye_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.innerHTML = svgEyeOff;
    } else {
        input.type = 'password';
        if (icon) icon.innerHTML = svgEye;
    }
}

// Mostrar/ocultar contraseña en modal editar
function toggleEditPassword() {
    var input = document.getElementById('edit_contrasena');
    var icon = document.getElementById('edit_eye_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.innerHTML = svgEyeOff;
    } else {
        input.type = 'password';
        if (icon) icon.innerHTML = svgEye;
    }
}

// Modal editar usuario
var modalEditarUsuario = document.getElementById('modalEditarUsuario');
var cerrarModalEditarUsuario = document.getElementById('cerrarModalEditarUsuario');

cerrarModalEditarUsuario.onclick = function() { 
    modalEditarUsuario.style.display = 'none'; 
}

window.onclick = function(event) {
    if (event.target == modalEditarUsuario) { 
        modalEditarUsuario.style.display = 'none'; 
    }
}

function editarUsuario(id, cedula, nombre, apellido, cargo, contrasena, id_pais, id_depart, id_ciudad) {
    document.getElementById('edit_cedula').value = cedula;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_apellido').value = apellido;
    document.getElementById('edit_cargo').value = cargo;
    document.getElementById('edit_contrasena').value = contrasena;
    document.getElementById('edit_id_pais').value = id_pais;
    document.getElementById('edit_id_depart').value = id_depart;
    
    // Configurar acción del formulario usando la ruta correcta de Laravel
    document.getElementById('formEditarUsuario').action = '/usuarios/' + id;
    
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
    
    modalEditarUsuario.style.display = 'block';
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