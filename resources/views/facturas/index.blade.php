@extends('layouts.app')

@section('title', 'Listado de Facturas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Facturas</h2>
        <a href="{{ route('facturacion.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-cart"></i> Volver a Facturación
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Filtros de búsqueda -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('facturas.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="busqueda" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="busqueda" name="busqueda" 
                           value="{{ request('busqueda') }}" 
                           placeholder="Prefijo, consecutivo o cliente...">
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="todas" {{ request('estado') == 'todas' ? 'selected' : '' }}>Todas</option>
                        <option value="activa" {{ request('estado', 'activa') == 'activa' ? 'selected' : '' }}>Activas</option>
                        <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anuladas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="per_page" class="form-label">Mostrar</label>
                    <select class="form-select" id="per_page" name="per_page">
                        <option value="20" {{ request('per_page', '20') == '20' ? 'selected' : '' }}>20 por página</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 por página</option>
                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 por página</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Mostrar todas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Información de resultados -->
            @if($facturas->count() > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        @if($paginatedFacturas)
                            <small class="text-muted">
                                Mostrando {{ ($paginatedFacturas->currentPage() - 1) * $paginatedFacturas->perPage() + 1 }} 
                                a {{ min($paginatedFacturas->currentPage() * $paginatedFacturas->perPage(), $paginatedFacturas->total()) }} 
                                de {{ $paginatedFacturas->total() }} facturas
                            </small>
                        @else
                            <small class="text-muted">
                                Mostrando todas las {{ $facturas->count() }} facturas
                            </small>
                        @endif
                    </div>
                    <div>
                        @if(request('busqueda') || request('estado') !== 'activa')
                            <span class="badge bg-info">
                                <i class="fas fa-filter"></i> Filtros aplicados
                            </span>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Prefijo</th>
                            <th>Consecutivo</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Atendido por</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                            <tr>
                                <td>{{ $factura->prefijo }}</td>
                                <td>{{ $factura->consecutivo }}</td>
                                <td>
                                    @if($factura->fecha_factura)
                                        {{ \Carbon\Carbon::parse($factura->fecha_factura)->setTimezone('America/Bogota')->format('d/m/Y H:i') }}
                                    @elseif($factura->created_at)
                                        {{ \Carbon\Carbon::parse($factura->created_at)->setTimezone('America/Bogota')->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Sin fecha</span>
                                    @endif
                                </td>
                                <td>{{ $factura->nombre_cliente ?? 'N/A' }}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ Str::limit($factura->productos, 50) }}
                                    </small>
                                </td>
                                <td>{{ $factura->atendido_por ?? 'N/A' }}</td>
                                <td class="fw-bold">${{ number_format($factura->total_factura, 2) }}</td>
                                <td>
                                    @if($factura->estado === 'activa')
                                        <span class="badge bg-success">{{ ucfirst($factura->estado) }}</span>
                                    @elseif($factura->estado === 'anulado')
                                        <span class="badge bg-danger">{{ ucfirst($factura->estado) }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($factura->estado) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($factura->estado !== 'anulado')
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#anularModal{{ $factura->id_fact }}">
                                            <i class="fas fa-times"></i> Anular
                                        </button>
                                        
                                        <!-- Modal de confirmación -->
                                        <div class="modal fade" id="anularModal{{ $factura->id_fact }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar Anulación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>¿Está seguro que desea anular la factura <strong>{{ $factura->prefijo }}-{{ $factura->consecutivo }}</strong>?</p>
                                                        <p><strong>Cliente:</strong> {{ $factura->nombre_cliente }}</p>
                                                        <p><strong>Total:</strong> ${{ number_format($factura->total_factura, 2) }}</p>
                                                        <div class="alert alert-warning">
                                                            <small><i class="fas fa-exclamation-triangle"></i> Esta acción no se puede deshacer.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <a href="{{ route('facturas.anular', $factura->id_fact) }}" 
                                                           class="btn btn-danger">
                                                            <i class="fas fa-times"></i> Anular Factura
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <p>No hay facturas registradas</p>
                                        <a href="{{ route('facturacion.index') }}" class="btn btn-primary">
                                            Crear Primera Factura
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($paginatedFacturas && $paginatedFacturas->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $paginatedFacturas->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Total Facturas</h5>
                            <h3 class="mb-0">{{ $estadisticas->total_facturas ?? 0 }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Activas</h5>
                            <h3 class="mb-0">{{ $estadisticas->facturas_activas ?? 0 }}</h3>
                            <small>Total en BD</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Anuladas</h5>
                            <h3 class="mb-0">{{ $estadisticas->facturas_anuladas ?? 0 }}</h3>
                            <small>Total en BD</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Total Vendido</h5>
                            <h3 class="mb-0">${{ number_format($estadisticas->total_vendido ?? 0, 0) }}</h3>
                            <small>Solo activas</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($facturas->count() > 0 && ($paginatedFacturas || request('busqueda') || request('estado') !== 'activa'))
        <!-- Estadísticas de la página/filtro actual -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-title text-muted mb-2">
                            <i class="fas fa-filter"></i> Resultados mostrados en esta vista
                        </h6>
                        <div class="row text-center">
                            <div class="col-3">
                                <strong>{{ $facturas->count() }}</strong>
                                <small class="d-block text-muted">facturas</small>
                            </div>
                            <div class="col-3">
                                <strong>{{ $facturas->where('estado', 'activa')->count() }}</strong>
                                <small class="d-block text-muted">activas</small>
                            </div>
                            <div class="col-3">
                                <strong>{{ $facturas->where('estado', 'anulado')->count() }}</strong>
                                <small class="d-block text-muted">anuladas</small>
                            </div>
                            <div class="col-3">
                                <strong>${{ number_format($facturas->where('estado', 'activa')->sum('total_factura'), 0) }}</strong>
                                <small class="d-block text-muted">vendido</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection