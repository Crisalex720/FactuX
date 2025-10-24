@extends('layouts.app')

@section('title', 'Gestión de Roles y Permisos')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-shield-check me-2"></i>Gestión de Roles y Permisos</h2>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver a Usuarios
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Roles Disponibles -->
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Roles Disponibles</h5>
                        </div>
                        <div class="card-body">
                            @foreach(\App\Services\RolePermissionService::getAllRoles() as $role => $description)
                                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                                    <div>
                                        <strong class="text-primary">{{ $description }}</strong>
                                        <br>
                                        <small class="text-muted">Clave: {{ $role }}</small>
                                    </div>
                                    <span class="badge bg-info">
                                        {{ count(\App\Services\RolePermissionService::getModulesForRole($role)) }} módulos
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Permisos por Rol -->
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-key me-2"></i>Permisos por Rol</h5>
                        </div>
                        <div class="card-body">
                            @foreach(\App\Services\RolePermissionService::getPermissions() as $role => $modules)
                                <div class="mb-4">
                                    <h6 class="text-primary border-bottom pb-2">
                                        {{ \App\Services\RolePermissionService::getAllRoles()[$role] ?? ucfirst($role) }}
                                    </h6>
                                    <div class="row">
                                        @foreach($modules as $module)
                                            <div class="col-6 mb-2">
                                                <span class="badge bg-success me-1">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    {{ \App\Services\RolePermissionService::getModulesDescription()[$module] ?? ucfirst($module) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usuarios Actuales -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Usuarios por Rol</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Nombre</th>
                                            <th>Rol</th>
                                            <th>Módulos Disponibles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trabajadores as $trabajador)
                                            <tr>
                                                <td>{{ number_format($trabajador->cedula, 0, '', '.') }}</td>
                                                <td>{{ $trabajador->nombre }} {{ $trabajador->apellido }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $trabajador->isMaster() ? 'danger' : 'primary' }}">
                                                        {{ \App\Services\RolePermissionService::getAllRoles()[strtolower($trabajador->cargo)] ?? ucfirst($trabajador->cargo) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @foreach($trabajador->getAvailableModules() as $module)
                                                        <small class="badge bg-light text-dark me-1">{{ $module }}</small>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection