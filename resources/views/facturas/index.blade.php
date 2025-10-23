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

    <div class="card">
        <div class="card-body">
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
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    @if($facturas->count() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Facturas</h5>
                        <h3>{{ $facturas->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Facturas Activas</h5>
                        <h3>{{ $facturas->where('estado', 'activa')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Facturas Anuladas</h5>
                        <h3>{{ $facturas->where('estado', 'anulado')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Vendido (Activas)</h5>
                        <h3>${{ number_format($facturas->where('estado', 'activa')->sum('total_factura'), 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection