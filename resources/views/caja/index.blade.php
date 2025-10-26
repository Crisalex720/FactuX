@extends('layouts.app')

@section('title', 'Gestión de Caja')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-cash-register"></i> Gestión de Caja
        </h2>
        <div>
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

    <div class="row">
        <!-- Estado Actual de la Caja -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Estado Actual de la Caja
                    </h5>
                </div>
                <div class="card-body">
                    @if($cajaAbierta)
                        <div class="alert alert-success">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-door-open"></i> Caja Abierta</h6>
                                    <p class="mb-1"><strong>Dinero Base:</strong> ${{ number_format($cajaAbierta->dinero_base, 0) }}</p>
                                    <p class="mb-1"><strong>Fecha Apertura:</strong> {{ $cajaAbierta->fecha_apertura->format('d/m/Y H:i') }}</p>
                                    <p class="mb-1"><strong>Tipo:</strong> {{ ucfirst($cajaAbierta->tipo_cierre) }}</p>
                                    <p class="mb-0"><strong>Abierto por:</strong> {{ $cajaAbierta->trabajadorApertura->nombre }} {{ $cajaAbierta->trabajadorApertura->apellido }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-chart-line"></i> Ventas del Período</h6>
                                    @php
                                        $ventasActuales = $cajaAbierta->calcularTotalVentas();
                                    @endphp
                                    <p class="mb-1"><strong>Ventas Actuales:</strong> ${{ number_format($ventasActuales, 0) }}</p>
                                    <p class="mb-1"><strong>Total Esperado:</strong> ${{ number_format($cajaAbierta->dinero_base + $ventasActuales, 0) }}</p>
                                    
                                    @if($cajaAbierta->tipo_cierre === 'diario')
                                        <p class="mb-0"><strong>Cierre Programado:</strong> {{ $cajaAbierta->fecha_apertura->copy()->addDay()->setTime(6, 0, 0)->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cerrarCajaModal">
                                    <i class="fas fa-door-closed"></i> Cerrar Caja
                                </button>
                                <a href="{{ route('caja.show', $cajaAbierta->id_caja) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Ver Detalles
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-door-closed"></i> No hay caja abierta</h6>
                            <p class="mb-3">Para comenzar a trabajar, debe abrir una caja registradora.</p>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#abrirCajaModal">
                                <i class="fas fa-door-open"></i> Abrir Caja
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resumen Rápido -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Resumen de Hoy
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $ventasHoy = \DB::table('factura as f')
                            ->join('lista_prod as lp', 'f.id_fact', '=', 'lp.id_fact')
                            ->join('producto as p', 'lp.id_producto', '=', 'p.id_producto')
                            ->where('f.estado', 'activa')
                            ->whereDate('f.created_at', today())
                            ->sum(\DB::raw('lp.cantidad * p.precio_ventap'));
                        
                        $facturasHoy = \DB::table('factura')
                            ->where('estado', 'activa')
                            ->whereDate('created_at', today())
                            ->count();
                    @endphp
                    <div class="text-center">
                        <div class="mb-3">
                            <h4 class="text-success">${{ number_format($ventasHoy, 0) }}</h4>
                            <small class="text-muted">Ventas de Hoy</small>
                        </div>
                        <div>
                            <h5 class="text-primary">{{ $facturasHoy }}</h5>
                            <small class="text-muted">Facturas Hoy</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Cierres -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">
                <i class="fas fa-history"></i> Últimos Cierres de Caja
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Apertura</th>
                            <th>Cierre</th>
                            <th>Base</th>
                            <th>Ventas</th>
                            <th>Contado</th>
                            <th>Diferencia</th>
                            <th>Cerrado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cajasCerradas as $caja)
                            <tr>
                                <td>{{ $caja->id_caja }}</td>
                                <td>
                                    <span class="badge bg-{{ $caja->tipo_cierre === 'diario' ? 'primary' : 'info' }}">
                                        {{ ucfirst($caja->tipo_cierre) }}
                                    </span>
                                </td>
                                <td><small>{{ $caja->fecha_apertura->format('d/m/Y H:i') }}</small></td>
                                <td><small>{{ $caja->fecha_cierre->format('d/m/Y H:i') }}</small></td>
                                <td>${{ number_format($caja->dinero_base, 0) }}</td>
                                <td class="text-success fw-bold">${{ number_format($caja->total_ventas, 0) }}</td>
                                <td>${{ number_format($caja->dinero_contado, 0) }}</td>
                                <td class="fw-bold {{ $caja->diferencia >= 0 ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format($caja->diferencia, 0) }}
                                </td>
                                <td><small>{{ $caja->trabajadorCierre->nombre ?? 'N/A' }}</small></td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('caja.show', $caja->id_caja) }}" class="btn btn-outline-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('caja.reporte-pdf', $caja->id_caja) }}" class="btn btn-outline-success" title="Descargar PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        @if(in_array(Auth::guard('trabajador')->user()->cargo, ['maestro', 'ceo', 'admin']))
                                            <form action="{{ route('caja.destroy', $caja->id_caja) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar" 
                                                        onclick="return confirm('¿Está seguro de eliminar este cierre de caja?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay cierres de caja registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Abrir Caja -->
<div class="modal fade" id="abrirCajaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-door-open"></i> Abrir Caja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('caja.abrir') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="fas fa-dollar-sign"></i> Dinero Base <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="dinero_base" class="form-control" step="0.01" min="0" required 
                               placeholder="Cantidad inicial en caja">
                        <small class="text-muted">Dinero con el que inicia la jornada</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo de Cierre</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_cierre" id="diario" value="diario" checked onchange="toggleFechaPersonalizada()">
                            <label class="form-check-label" for="diario">
                                <strong>Diario</strong> <small class="text-muted">(6:00 AM - 6:00 AM del día siguiente)</small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_cierre" id="personalizado" value="personalizado" onchange="toggleFechaPersonalizada()">
                            <label class="form-check-label" for="personalizado">
                                <strong>Personalizado</strong> <small class="text-muted">(Definir fecha de apertura)</small>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-none" id="fecha_personalizada_div">
                        <label class="form-label fw-bold">Fecha y Hora de Apertura</label>
                        <input type="datetime-local" name="fecha_apertura" class="form-control">
                        <small class="text-muted">Solo para cierre personalizado</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-door-open"></i> Abrir Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cerrar Caja -->
@if($cajaAbierta)
<div class="modal fade" id="cerrarCajaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-door-closed"></i> Cerrar Caja
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('caja.cerrar', $cajaAbierta->id_caja) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Información de la caja -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Dinero Base:</strong> ${{ number_format($cajaAbierta->dinero_base, 0) }}</p>
                                <p class="mb-1"><strong>Ventas del Período:</strong> ${{ number_format($cajaAbierta->calcularTotalVentas(), 0) }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total Esperado:</strong> ${{ number_format($cajaAbierta->dinero_base + $cajaAbierta->calcularTotalVentas(), 0) }}</p>
                                <p class="mb-0"><strong>Tipo:</strong> {{ ucfirst($cajaAbierta->tipo_cierre) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-coins"></i> Dinero Contado <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="dinero_contado" class="form-control" step="0.01" min="0" required 
                                       placeholder="Total contado en caja">
                                <small class="text-muted">Incluye el dinero base + ventas</small>
                            </div>
                        </div>
                        
                        @if($cajaAbierta->tipo_cierre === 'personalizado')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha y Hora de Cierre</label>
                                <input type="datetime-local" name="fecha_cierre" class="form-control" required>
                                <small class="text-muted">Máximo un mes después de la apertura</small>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Observaciones del cierre (opcional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-door-closed"></i> Cerrar Caja
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function toggleFechaPersonalizada() {
    const personalizado = document.getElementById('personalizado').checked;
    const fechaDiv = document.getElementById('fecha_personalizada_div');
    
    if (personalizado) {
        fechaDiv.classList.remove('d-none');
        document.querySelector('input[name="fecha_apertura"]').required = true;
    } else {
        fechaDiv.classList.add('d-none');
        document.querySelector('input[name="fecha_apertura"]').required = false;
    }
}
</script>
@endsection