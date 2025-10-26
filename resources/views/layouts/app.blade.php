<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Facturación Fácilito')</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/inventario.css') }}">
    
    @yield('styles')
</head>
<body>
    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand font-weight-bold text-primary">FactuX</a>
            
            @auth('trabajador')
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    @if(Auth::guard('trabajador')->user()->hasPermission('usuarios'))
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('usuarios.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('usuarios.index') }}" 
                           {{ request()->routeIs('usuarios.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Usuarios
                           <i class="bi bi-people-fill ms-1"></i>
                        </a>
                    </li>
                    @endif

                    @if(Auth::guard('trabajador')->user()->hasPermission('facturacion'))
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('facturacion.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('facturacion.index') }}"
                           {{ request()->routeIs('facturacion.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Facturación
                           <i class="bi bi-receipt-cutoff ms-1"></i>
                        </a>
                    </li>
                    @endif

                    @if(Auth::guard('trabajador')->user()->hasPermission('facturas'))
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('facturas.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('facturas.index') }}"
                           {{ request()->routeIs('facturas.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Facturas
                           <i class="bi bi-file-earmark-text ms-1"></i>
                        </a>
                    </li>
                    @endif

                    @if(Auth::guard('trabajador')->user()->hasPermission('reportes'))
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('reportes.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('reportes.index') }}"
                           {{ request()->routeIs('reportes.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Reportes
                           <i class="bi bi-file-earmark-bar-graph ms-1"></i>
                        </a>
                    </li>
                    @endif

                    @if(Auth::guard('trabajador')->user()->hasPermission('inventario'))
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('inventario.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('inventario.index') }}" 
                           {{ request()->routeIs('inventario.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Inventario
                           <i class="bi bi-boxes ms-1"></i>
                        </a>
                    </li>
                    @endif

                    @if(Auth::guard('trabajador')->user()->hasPermission('caja'))
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('caja.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('caja.index') }}" 
                           {{ request()->routeIs('caja.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Caja
                           <i class="bi bi-cash-coin ms-1"></i>
                        </a>
                    </li>
                    @endif

                    @if(Auth::guard('trabajador')->user()->hasPermission('clientes'))
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('clientes.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('clientes.index') }}"
                           {{ request()->routeIs('clientes.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Clientes
                           <i class="bi bi-people-fill ms-1"></i>
                        </a>
                    </li>
                    @endif
                </ul>
                
                <!-- Información del usuario -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>{{ Auth::guard('trabajador')->user()->nombre }} {{ Auth::guard('trabajador')->user()->apellido }}</span>
                            <small class="text-muted ms-1">({{ Auth::guard('trabajador')->user()->cargo }})</small>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="bi bi-person me-2"></i>Mi Perfil
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </nav>

    <!-- Mensajes de éxito/error -->
    @if(session('success') && str_contains(session('success'), 'Factura registrada'))
        <div id="modalMensaje" class="modal" style="display:block;">
            <div class="modal-content" style="max-width:400px;margin:auto;">
                <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div id="modalMensaje" class="modal" style="display:block;">
            <div class="modal-content" style="max-width:400px;margin:auto;">
                <span class="close" onclick="this.parentElement.parentElement.style.display='none'">&times;</span>
                <div class="text-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Contenido principal -->
    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
