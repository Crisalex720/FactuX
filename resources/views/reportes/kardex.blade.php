@extends('layouts.app')

@section('title', 'Reporte KARDEX')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-clipboard-data me-2"></i>Reporte KARDEX de Inventario</h2>
        <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver a Reportes
        </a>
    </div>

    <!-- Formulario de filtros -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.kardex') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" 
                               value="{{ $fechaInicio->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" 
                               value="{{ $fechaFin->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Producto (Opcional)</label>
                        <select name="producto_id" class="form-control">
                            <option value="">Todos los productos</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto->id_producto }}" 
                                        {{ $productoId == $producto->id_producto ? 'selected' : '' }}>
                                    {{ $producto->nombre_prod }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($kardexData))
        <!-- Botones de acción -->
        <div class="mb-3">
            <form method="GET" action="{{ route('reportes.kardex') }}" style="display: inline;">
                <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio->format('Y-m-d') }}">
                <input type="hidden" name="fecha_fin" value="{{ $fechaFin->format('Y-m-d') }}">
                @if($productoId)
                    <input type="hidden" name="producto_id" value="{{ $productoId }}">
                @endif
                <button type="submit" name="generar_pdf" value="1" class="btn btn-info me-2">
                    <i class="bi bi-eye me-1"></i>Ver PDF
                </button>
                <button type="submit" name="generar_pdf" value="download" class="btn btn-danger">
                    <i class="bi bi-download me-1"></i>Descargar PDF
                </button>
            </form>
        </div>

        <!-- Tabla de resultados -->
        @foreach($kardexData as $productoId => $datos)
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-box me-2"></i>{{ $datos['producto'] }}
                        <small class="ms-3">Precio: ${{ number_format($datos['precio_unitario'], 2) }}</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="alert alert-info mb-0">
                                <strong>Stock Inicial:</strong> {{ $datos['stock_inicial'] }} unidades
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning mb-0">
                                <strong>Total Vendido:</strong> {{ collect($datos['movimientos'])->sum('cantidad') }} unidades
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-success mb-0">
                                <strong>Stock Final:</strong> {{ $datos['stock_final'] }} unidades
                            </div>
                        </div>
                    </div>

                    @if(!empty($datos['movimientos']))
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Factura</th>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Valor Unit.</th>
                                        <th>Valor Total</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($datos['movimientos'] as $movimiento)
                                        <tr>
                                            <td>{{ $movimiento['fecha'] }}</td>
                                            <td>{{ $movimiento['factura'] }}</td>
                                            <td>{{ $movimiento['cliente'] }}</td>
                                            <td>
                                                <span class="badge bg-danger">{{ $movimiento['tipo'] }}</span>
                                            </td>
                                            <td class="text-center">{{ $movimiento['cantidad'] }}</td>
                                            <td class="text-end">${{ number_format($movimiento['valor_unitario'], 2) }}</td>
                                            <td class="text-end">${{ number_format($movimiento['valor_total'], 2) }}</td>
                                            <td class="text-center">
                                                <strong>{{ $movimiento['saldo'] }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>No hay movimientos registrados para este producto en el período seleccionado.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                <h4 class="text-muted">No hay datos para mostrar</h4>
                <p class="text-muted">No se encontraron movimientos de inventario en el período seleccionado.</p>
            </div>
        </div>
    @endif
</div>
@endsection