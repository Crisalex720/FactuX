@extends('layouts.app')

@section('title', 'Detalles de Caja #' . $caja->id_caja)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-receipt"></i> Detalles de Caja #{{ $caja->id_caja }}
        </h2>
        <div>
            <a href="{{ route('caja.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @if($caja->estado === 'cerrada')
                <a href="{{ route('caja.reporte-pdf', $caja->id_caja) }}" class="btn btn-success">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información General -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-{{ $caja->estado === 'abierta' ? 'success' : 'primary' }} text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle"></i> Información General
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Estado:</label>
                        <span class="badge bg-{{ $caja->estado === 'abierta' ? 'success' : 'primary' }} fs-6">
                            {{ ucfirst($caja->estado) }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Tipo de Cierre:</label>
                        <p class="mb-0">{{ ucfirst($caja->tipo_cierre) }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Fecha de Apertura:</label>
                        <p class="mb-0">{{ $caja->fecha_apertura->format('d/m/Y H:i:s') }}</p>
                    </div>
                    
                    @if($caja->fecha_cierre)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Fecha de Cierre:</label>
                        <p class="mb-0">{{ $caja->fecha_cierre->format('d/m/Y H:i:s') }}</p>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Abierto por:</label>
                        <p class="mb-0">{{ $caja->trabajadorApertura->nombre }} {{ $caja->trabajadorApertura->apellido }}</p>
                    </div>
                    
                    @if($caja->trabajadorCierre)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Cerrado por:</label>
                        <p class="mb-0">{{ $caja->trabajadorCierre->nombre }} {{ $caja->trabajadorCierre->apellido }}</p>
                    </div>
                    @endif
                    
                    @if($caja->observaciones)
                    <div class="mb-0">
                        <label class="fw-bold text-muted">Observaciones:</label>
                        <p class="mb-0">{{ $caja->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator"></i> Resumen Financiero
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-primary">${{ number_format($caja->dinero_base, 0) }}</h5>
                                    <small class="text-muted">Dinero Base</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="text-success">${{ number_format($caja->total_ventas, 0) }}</h5>
                                    <small class="text-muted">Total Ventas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($caja->estado === 'cerrada')
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card bg-warning bg-opacity-25">
                                <div class="card-body text-center">
                                    <h5 class="text-warning">${{ number_format($caja->dinero_contado, 0) }}</h5>
                                    <small class="text-muted">Dinero Contado</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-{{ $caja->diferencia >= 0 ? 'success' : 'danger' }} bg-opacity-25">
                                <div class="card-body text-center">
                                    <h5 class="text-{{ $caja->diferencia >= 0 ? 'success' : 'danger' }}">
                                        ${{ number_format($caja->diferencia, 0) }}
                                    </h5>
                                    <small class="text-muted">
                                        {{ $caja->diferencia >= 0 ? 'Sobrante' : 'Faltante' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <strong>Esperado:</strong><br>
                                    ${{ number_format($caja->dinero_base + $caja->total_ventas, 0) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Contado:</strong><br>
                                    ${{ number_format($caja->dinero_contado, 0) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Diferencia:</strong><br>
                                    <span class="text-{{ $caja->diferencia >= 0 ? 'success' : 'danger' }}">
                                        ${{ number_format($caja->diferencia, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="mt-3">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-clock"></i> Caja Abierta</h6>
                            <p class="mb-0">Esta caja aún está abierta. Los datos se actualizarán al cerrarla.</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Facturas del Período -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h6 class="mb-0">
                <i class="fas fa-file-invoice"></i> Facturas del Período 
                <span class="badge bg-light text-dark">{{ $facturas->count() }}</span>
            </h6>
        </div>
        <div class="card-body p-0">
            @if($facturas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Consecutivo</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Atendido por</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facturas as $factura)
                                <tr>
                                    <td>
                                        <strong>{{ $factura->prefijo }}-{{ $factura->consecutivo }}</strong>
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($factura->fecha_factura ?? $factura->created_at)->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>{{ $factura->nombre_cliente ?? 'N/A' }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ Str::limit($factura->productos, 40) }}
                                        </small>
                                    </td>
                                    <td><small>{{ $factura->atendido_por ?? 'N/A' }}</small></td>
                                    <td class="fw-bold text-success">
                                        ${{ number_format($factura->total_factura, 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">TOTAL:</th>
                                <th class="text-success">
                                    ${{ number_format($facturas->sum('total_factura'), 0) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Estadísticas de Facturas -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <strong>{{ $facturas->count() }}</strong>
                            <small class="d-block text-muted">Total Facturas</small>
                        </div>
                        <div class="col-md-3">
                            <strong>${{ number_format($facturas->max('total_factura') ?? 0, 0) }}</strong>
                            <small class="d-block text-muted">Factura Mayor</small>
                        </div>
                        <div class="col-md-3">
                            <strong>${{ number_format($facturas->min('total_factura') ?? 0, 0) }}</strong>
                            <small class="d-block text-muted">Factura Menor</small>
                        </div>
                        <div class="col-md-3">
                            <strong>${{ number_format($facturas->avg('total_factura') ?? 0, 0) }}</strong>
                            <small class="d-block text-muted">Promedio</small>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p>No hay facturas en este período</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection