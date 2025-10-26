@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@push('styles')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="{{ asset('css/usuarios.css') }}" rel="stylesheet">
<style>
.foto-preview img {
    transition: all 0.3s ease;
}
.foto-preview img:hover {
    transform: scale(1.1);
}
.card-header {
    border-radius: 0.375rem 0.375rem 0 0 !important;
}
.password-cell {
    position: relative;
}
.table th {
    background-color: #f8f9fa;
    border-top: none;
}
.user-photo {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border: 2px solid #dee2e6;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-users"></i> Gestión de Usuarios</h2>
        <div>
            @if(Auth::guard('trabajador')->user()->cargo === 'maestro')
                <a href="{{ route('usuarios.roles') }}" class="btn btn-info me-2">
                    <i class="fas fa-shield-alt"></i> Roles y Permisos
                </a>
            @endif
            <span class="badge bg-primary fs-6">
                <i class="fas fa-user"></i> {{ \App\Services\RolePermissionService::getAllRoles()[Auth::guard('trabajador')->user()->cargo] ?? ucfirst(Auth::guard('trabajador')->user()->cargo) }}
            </span>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulario de Registro Mejorado -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus"></i> Registrar Nuevo Usuario</h5>
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('usuarios.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Columna 1: Datos Personales -->
                    <div class="col-md-6">
                        <div class="row g-3">
                            <!-- Fila 1: Cédula -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-id-card text-primary"></i> Cédula <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="cedula" class="form-control" required 
                                       value="{{ old('cedula') }}" placeholder="Número de cédula">
                            </div>
                            <!-- Fila 2: Nombre -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user text-primary"></i> Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control" required 
                                       value="{{ old('nombre') }}" placeholder="Nombre completo">
                            </div>
                            <!-- Fila 3: Apellido -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user text-primary"></i> Apellido <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="apellido" class="form-control" required 
                                       value="{{ old('apellido') }}" placeholder="Apellidos">
                            </div>
                            <!-- Fila 4: Rol/Cargo -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-briefcase text-primary"></i> Rol/Cargo <span class="text-danger">*</span>
                                </label>
                                <select name="cargo" class="form-select" required>
                                    <option value="">Seleccionar rol...</option>
                                    @foreach($rolesDisponibles as $roleKey => $roleDesc)
                                        <option value="{{ $roleKey }}" {{ old('cargo') == $roleKey ? 'selected' : '' }}>
                                            {{ $roleDesc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna 2: Datos de Ubicación y Acceso -->
                    <div class="col-md-6">
                        <div class="row g-3">
                            <!-- Fila 1: Foto de Perfil -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-camera text-primary"></i> Foto de Perfil <span class="text-muted">(Opcional)</span>
                                </label>
                                <div class="d-flex align-items-center">
                                    <div class="foto-preview me-3">
                                        <img id="preview_foto" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTIiIGZpbGw9IiNlMGUwZTAiLz4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMCIgcj0iMyIgZmlsbD0iIzk5OTk5OSIvPgo8cGF0aCBkPSJtNCAxOSAuNS0xLjVBMi41IDIuNSAwIDAgMSA3IDEzaDEwYTIuNSAyLjUgMCAwIDEgMi41IDQuNUwyMCAxOSIgZmlsbD0iIzk5OTk5OSIvPgo8L3N2Zz4K" 
                                             alt="Preview" class="rounded-circle border" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" name="foto_perfil" id="foto_perfil" 
                                               class="form-control" accept="image/*" onchange="previewFoto(this)">
                                        <small class="text-muted">JPG, PNG, máximo 2MB</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Fila 2: Contraseña -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-lock text-primary"></i> Contraseña <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" name="contrasena" id="reg_contrasena" 
                                           class="form-control" required placeholder="Mínimo 4 caracteres">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleRegPassword()">
                                        <i id="reg_eye_icon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Fila 3: País -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-globe text-primary"></i> País <span class="text-danger">*</span>
                                </label>
                                <select name="id_pais" class="form-select" required>
                                    <option value="">Seleccionar país...</option>
                                    @foreach($paises as $pais)
                                        <option value="{{ $pais->id_pais }}" {{ old('id_pais') == $pais->id_pais ? 'selected' : '' }}>
                                            {{ $pais->nombre_pais }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Fila 4: Departamento -->
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-map-marker-alt text-primary"></i> Departamento <span class="text-danger">*</span>
                                </label>
                                <select name="id_depart" class="form-select" required>
                                    <option value="">Seleccionar departamento...</option>
                                    @foreach($departamentos as $departamento)
                                        <option value="{{ $departamento->id_depart }}" {{ old('id_depart') == $departamento->id_depart ? 'selected' : '' }}>
                                            {{ $departamento->nombre_depart }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Fila adicional para Ciudad y Botón -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <i class="fas fa-city text-primary"></i> Ciudad <span class="text-danger">*</span>
                        </label>
                        <select name="id_ciudad" class="form-select" required>
                            <option value="">Seleccionar ciudad...</option>
                            @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id_ciudad }}" data-depart="{{ $ciudad->id_depart }}" 
                                        {{ old('id_ciudad') == $ciudad->id_ciudad ? 'selected' : '' }}>
                                    {{ $ciudad->nombre_ciudad }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-user-plus"></i> Registrar Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Usuarios</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Rol</th>
                            <th>País</th>
                            <th>Departamento</th>
                            <th>Ciudad</th>
                            <th>Contraseña</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr class="{{ $usuario->cedula == '999999999' ? 'table-warning' : '' }}" 
                                @if($usuario->cedula == '999999999') title="Usuario Maestro del Sistema" @endif>
                                <td>{{ $usuario->id_trab }}</td>
                                <td>
                                    <img src="{{ $usuario->foto_perfil ? asset('uploads/perfiles/' . $usuario->foto_perfil) : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTIiIGZpbGw9IiNlMGUwZTAiLz4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMCIgcj0iMyIgZmlsbD0iIzk5OTk5OSIvPgo8cGF0aCBkPSJtNCAxOSAuNS0xLjVBMi41IDIuNSAwIDAgMSA3IDEzaDEwYTIuNSAyLjUgMCAwIDEgMi41IDQuNUwyMCAxOSIgZmlsbD0iIzk5OTk5OSIvPgo8L3N2Zz4K' }}" 
                                         alt="Foto" class="user-photo rounded-circle">
                                </td>
                                <td>
                                    {{ $usuario->cedula }}
                                    @if($usuario->cedula == '999999999')
                                        <span class="badge bg-danger ms-2">
                                            <i class="fas fa-crown"></i> MAESTRO
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $usuario->nombre }}</td>
                                <td>{{ $usuario->apellido }}</td>
                                <td>
                                    <span class="badge bg-{{ $usuario->cargo === 'maestro' ? 'danger' : ($usuario->cargo === 'ceo' ? 'warning' : 'primary') }}">
                                        {{ \App\Services\RolePermissionService::getAllRoles()[strtolower($usuario->cargo)] ?? ucfirst($usuario->cargo) }}
                                    </span>
                                </td>
                                <td>{{ $usuario->pais->nombre_pais ?? 'N/A' }}</td>
                                <td>{{ $usuario->departamento->nombre_depart ?? 'N/A' }}</td>
                                <td>{{ $usuario->ciudad->nombre_ciudad ?? 'N/A' }}</td>
                                <td class="password-cell">
                                    <div class="d-flex align-items-center">
                                        <span class="password-mask me-2" id="pw_mask_{{ $usuario->id_trab }}">••••••••</span>
                                        <span class="d-none me-2" id="pw_real_{{ $usuario->id_trab }}">{{ $usuario->contraseña }}</span>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1" onclick="togglePassword({{ $usuario->id_trab }})">
                                            <i id="eye_icon_{{ $usuario->id_trab }}" class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $currentUser = Auth::guard('trabajador')->user();
                                        $isMasterUser = $usuario->cedula == '999999999';
                                        $isCurrentUserMaster = $currentUser->cargo === 'maestro';
                                        $isOwnUser = $usuario->id_trab == $currentUser->id_trab;
                                    @endphp
                                    
                                    @if($isMasterUser && !$isCurrentUserMaster)
                                        <!-- Usuario maestro: no mostrar botones si no eres maestro -->
                                        <span class="text-muted">
                                            <i class="fas fa-shield-alt"></i> Protegido
                                        </span>
                                    @elseif($isMasterUser && $isCurrentUserMaster)
                                        <!-- Usuario maestro editándose a sí mismo -->
                                        <button type="button" class="btn btn-warning btn-sm" onclick="editarUsuario(
                                            '{{ $usuario->id_trab }}',
                                            '{{ $usuario->cedula }}',
                                            '{{ $usuario->nombre }}',
                                            '{{ $usuario->apellido }}',
                                            '{{ $usuario->cargo }}',
                                            '{{ $usuario->contraseña }}',
                                            '{{ $usuario->id_pais }}',
                                            '{{ $usuario->id_depart }}',
                                            '{{ $usuario->id_ciudad }}',
                                            '{{ $usuario->foto_perfil }}'
                                        )">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <span class="text-muted ms-1">
                                            <small><i class="fas fa-info-circle"></i> No eliminable</small>
                                        </span>
                                    @else
                                        <!-- Usuarios normales -->
                                        <button type="button" class="btn btn-warning btn-sm" onclick="editarUsuario(
                                            '{{ $usuario->id_trab }}',
                                            '{{ $usuario->cedula }}',
                                            '{{ $usuario->nombre }}',
                                            '{{ $usuario->apellido }}',
                                            '{{ $usuario->cargo }}',
                                            '{{ $usuario->contraseña }}',
                                            '{{ $usuario->id_pais }}',
                                            '{{ $usuario->id_depart }}',
                                            '{{ $usuario->id_ciudad }}',
                                            '{{ $usuario->foto_perfil }}'
                                        )">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        
                                        @if(!$isOwnUser)
                                            <form action="{{ route('usuarios.destroy', $usuario->id_trab) }}" method="POST" style="display: inline; margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario {{ $usuario->nombre }}?');">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted ms-1">
                                                <small><i class="fas fa-user-shield"></i> Tu usuario</small>
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p>No hay usuarios registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editarForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cédula</label>
                            <input type="number" name="cedula" id="edit_cedula" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Apellido</label>
                            <input type="text" name="apellido" id="edit_apellido" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Rol/Cargo</label>
                            <select name="cargo" id="edit_cargo" class="form-select" required>
                                @foreach($rolesDisponibles as $roleKey => $roleDesc)
                                    <option value="{{ $roleKey }}">{{ $roleDesc }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Foto de Perfil</label>
                            <div class="d-flex align-items-center">
                                <div class="foto-preview me-3">
                                    <img id="edit_preview_foto" src="" alt="Preview" 
                                         class="rounded-circle border" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" name="foto_perfil" id="edit_foto_perfil" 
                                           class="form-control" accept="image/*" onchange="previewEditFoto(this)">
                                    <small class="text-muted">Dejar vacío para mantener la foto actual</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Contraseña</label>
                            <div class="input-group">
                                <input type="password" name="contrasena" id="edit_contrasena" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleEditPassword()">
                                    <i id="edit_eye_icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">País</label>
                            <select name="id_pais" id="edit_id_pais" class="form-select" required>
                                @foreach($paises as $pais)
                                    <option value="{{ $pais->id_pais }}">{{ $pais->nombre_pais }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Departamento</label>
                            <select name="id_depart" id="edit_id_depart" class="form-select" required>
                                @foreach($departamentos as $departamento)
                                    <option value="{{ $departamento->id_depart }}">{{ $departamento->nombre_depart }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ciudad</label>
                            <select name="id_ciudad" id="edit_id_ciudad" class="form-select" required>
                                @foreach($ciudades as $ciudad)
                                    <option value="{{ $ciudad->id_ciudad }}" data-depart="{{ $ciudad->id_depart }}">{{ $ciudad->nombre_ciudad }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Preview de foto en registro
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview_foto').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview de foto en edición
function previewEditFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit_preview_foto').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Toggle password registro
function toggleRegPassword() {
    const passwordInput = document.getElementById('reg_contrasena');
    const eyeIcon = document.getElementById('reg_eye_icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'fas fa-eye';
    }
}

// Toggle password edición
function toggleEditPassword() {
    const passwordInput = document.getElementById('edit_contrasena');
    const eyeIcon = document.getElementById('edit_eye_icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'fas fa-eye';
    }
}

// Toggle password en tabla
function togglePassword(userId) {
    const maskElement = document.getElementById('pw_mask_' + userId);
    const realElement = document.getElementById('pw_real_' + userId);
    const eyeIcon = document.getElementById('eye_icon_' + userId);
    
    if (maskElement.classList.contains('d-none')) {
        maskElement.classList.remove('d-none');
        realElement.classList.add('d-none');
        eyeIcon.className = 'fas fa-eye';
    } else {
        maskElement.classList.add('d-none');
        realElement.classList.remove('d-none');
        eyeIcon.className = 'fas fa-eye-slash';
    }
}

// Función editar usuario
function editarUsuario(id, cedula, nombre, apellido, cargo, contrasena, idPais, idDepart, idCiudad, foto) {
    document.getElementById('editarForm').action = `/usuarios/${id}`;
    document.getElementById('edit_cedula').value = cedula;
    document.getElementById('edit_nombre').value = nombre;
    document.getElementById('edit_apellido').value = apellido;
    document.getElementById('edit_cargo').value = cargo;
    document.getElementById('edit_contrasena').value = contrasena;
    document.getElementById('edit_id_pais').value = idPais;
    document.getElementById('edit_id_depart').value = idDepart;
    document.getElementById('edit_id_ciudad').value = idCiudad;
    
    // Establecer foto actual
    const fotoUrl = foto ? `/uploads/perfiles/${foto}` : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMiIgcj0iMTIiIGZpbGw9IiNlMGUwZTAiLz4KPGNpcmNsZSBjeD0iMTIiIGN5PSIxMCIgcj0iMyIgZmlsbD0iIzk5OTk5OSIvPgo8cGF0aCBkPSJtNCAxOSAuNS0xLjVBMi41IDIuNSAwIDAgMSA3IDEzaDEwYTIuNSAyLjUgMCAwIDEgMi41IDQuNUwyMCAxOSIgZmlsbD0iIzk5OTk5OSIvPgo8L3N2Zz4K';
    document.getElementById('edit_preview_foto').src = fotoUrl;
    
    // Mostrar modal
    new bootstrap.Modal(document.getElementById('editarModal')).show();
}

// Filtrar ciudades por departamento
document.addEventListener('DOMContentLoaded', function() {
    // Filtro para formulario de registro
    const paisSelect = document.querySelector('select[name="id_pais"]');
    const departSelect = document.querySelector('select[name="id_depart"]');
    const ciudadSelect = document.querySelector('select[name="id_ciudad"]');
    
    // Filtro para modal de edición
    const editPaisSelect = document.getElementById('edit_id_pais');
    const editDepartSelect = document.getElementById('edit_id_depart');
    const editCiudadSelect = document.getElementById('edit_id_ciudad');
    
    function filterCities(departamentoId, ciudadSelect) {
        const options = ciudadSelect.querySelectorAll('option');
        options.forEach(option => {
            if (option.value === '') return; // Mantener opción vacía
            const departOption = option.getAttribute('data-depart');
            if (departOption === departamentoId || departamentoId === '') {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        ciudadSelect.value = ''; // Reset selection
    }
    
    if (departSelect) {
        departSelect.addEventListener('change', function() {
            filterCities(this.value, ciudadSelect);
        });
    }
    
    if (editDepartSelect) {
        editDepartSelect.addEventListener('change', function() {
            filterCities(this.value, editCiudadSelect);
        });
    }
});
</script>
@endpush
@endsection