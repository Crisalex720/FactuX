<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Facturación Fácilito')</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/inventario.css') }}">
    
    @yield('styles')
</head>
<body>
    <!-- Barra de navegación superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand font-weight-bold text-primary">Facturación Fácilito</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('usuarios.*') ? 'btn-info disabled' : 'btn-outline-info' }} nav-btn" 
                           href="{{ route('usuarios.index') }}" 
                           {{ request()->routeIs('usuarios.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Usuarios
                           <i class="bi bi-people-fill ms-1"></i>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('facturacion.*') ? 'btn-primary disabled' : 'btn-outline-primary' }} nav-btn" 
                           href="{{ route('facturacion.index') }}"
                           {{ request()->routeIs('facturacion.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Facturación
                           <i class="bi bi-receipt-cutoff ms-1"></i>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('facturas.*') ? 'btn-warning disabled' : 'btn-outline-warning' }} nav-btn" 
                           href="{{ route('facturas.index') }}"
                           {{ request()->routeIs('facturas.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Facturas
                           <i class="bi bi-file-earmark-text ms-1"></i>
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('devoluciones.*') ? 'btn-secondary disabled' : 'btn-outline-secondary' }} nav-btn" 
                           href="#"
                           {{ request()->routeIs('devoluciones.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Devoluciones
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('inventario.*') ? 'btn-success disabled' : 'btn-outline-success' }} nav-btn" 
                           href="{{ route('inventario.index') }}" 
                           {{ request()->routeIs('inventario.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Inventario
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn {{ request()->routeIs('clientes.*') ? 'btn-info disabled' : 'btn-outline-info' }} nav-btn" 
                           href="#"
                           {{ request()->routeIs('clientes.*') ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                           Clientes
                        </a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn btn-outline-danger nav-btn" href="#">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
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
