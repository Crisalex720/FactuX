@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center">Módulo de Reportes</h2>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reportes Disponibles</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Reporte Kardex -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="bi bi-clipboard-data display-4 text-success"></i>
                                    </div>
                                    <h5 class="card-title">Reporte KARDEX</h5>
                                    <p class="card-text text-muted">
                                        Genera un reporte detallado del movimiento de inventario con entradas, salidas y saldos por producto.
                                    </p>
                                    <a href="{{ route('reportes.kardex') }}" class="btn btn-success">
                                        <i class="bi bi-graph-up me-1"></i>Generar KARDEX
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Próximos reportes -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="bi bi-bar-chart display-4 text-info"></i>
                                    </div>
                                    <h5 class="card-title">Ventas por Período</h5>
                                    <p class="card-text text-muted">
                                        Reporte de ventas totales agrupadas por fechas y productos más vendidos.
                                    </p>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-clock me-1"></i>Próximamente
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de clientes -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="bi bi-people display-4 text-warning"></i>
                                    </div>
                                    <h5 class="card-title">Clientes Frecuentes</h5>
                                    <p class="card-text text-muted">
                                        Listado de clientes con mayor número de compras y montos facturados.
                                    </p>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-clock me-1"></i>Próximamente
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Reporte de stock bajo -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                                    </div>
                                    <h5 class="card-title">Stock Bajo</h5>
                                    <p class="card-text text-muted">
                                        Productos con stock por debajo del mínimo establecido para reabastecimiento.
                                    </p>
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-clock me-1"></i>Próximamente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.display-4 {
    font-size: 3rem;
}
</style>
@endsection