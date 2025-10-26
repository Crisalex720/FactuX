@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>Mi Perfil
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="profile-avatar mb-3">
                                @if($trabajador->foto_perfil && file_exists(public_path('uploads/perfiles/' . $trabajador->foto_perfil)))
                                    <img src="{{ asset('uploads/perfiles/' . $trabajador->foto_perfil) }}" 
                                         alt="Foto de {{ $trabajador->nombre }}" 
                                         class="rounded-circle border border-3 border-primary shadow"
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle border border-3 border-primary d-flex align-items-center justify-content-center mx-auto shadow"
                                         style="width: 150px; height: 150px; background-color: #f8f9fa;">
                                        <i class="fas fa-user fa-4x text-primary"></i>
                                    </div>
                                @endif
                            </div>
                            <h5 class="text-primary">{{ $trabajador->nombre }} {{ $trabajador->apellido }}</h5>
                            <p class="text-muted">
                                <span class="badge bg-{{ $trabajador->cargo === 'maestro' ? 'danger' : 'primary' }} fs-6">
                                    {{ \App\Services\RolePermissionService::getAllRoles()[strtolower($trabajador->cargo)] ?? ucfirst($trabajador->cargo) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-primary mb-3">Información Personal</h6>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Cédula:</strong></div>
                                <div class="col-sm-8">{{ number_format($trabajador->cedula, 0, '', '.') }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Nombre Completo:</strong></div>
                                <div class="col-sm-8">{{ $trabajador->nombre }} {{ $trabajador->apellido }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Cargo:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-success">{{ ucwords($trabajador->cargo) }}</span>
                                </div>
                            </div>

                            @if($trabajador->pais)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>País:</strong></div>
                                <div class="col-sm-8">{{ ucwords($trabajador->pais->nombre_pais) }}</div>
                            </div>
                            @endif

                            @if($trabajador->departamento)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Departamento:</strong></div>
                                <div class="col-sm-8">{{ ucwords($trabajador->departamento->nombre_depart) }}</div>
                            </div>
                            @endif

                            @if($trabajador->ciudad)
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Ciudad:</strong></div>
                                <div class="col-sm-8">{{ ucwords($trabajador->ciudad->nombre_ciudad) }}</div>
                            </div>
                            @endif

                            <hr>

                            <!-- Actualizar Foto de Perfil -->
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-camera"></i> Foto de Perfil
                            </h6>
                            
                            <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="file" name="foto_perfil" class="form-control" accept="image/*" id="foto_perfil_update">
                                        <small class="text-muted">JPG, PNG, máximo 2MB</small>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-upload"></i> Actualizar Foto
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <h6 class="text-primary mb-3">
                                <i class="fas fa-chart-bar"></i> Estadísticas
                            </h6>
                            
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-receipt display-6 text-success"></i>
                                            <h5 class="mt-2">{{ $trabajador->facturas->count() }}</h5>
                                            <small class="text-muted">Facturas Generadas</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-cash-stack display-6 text-primary"></i>
                                            <h5 class="mt-2">${{ number_format($totalFacturado ?? 0, 2) }}</h5>
                                            <small class="text-muted">Total Facturado</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('inventario.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="bi bi-arrow-left me-1"></i>Volver al Sistema
                                </a>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection