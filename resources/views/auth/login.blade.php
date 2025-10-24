@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Logo/Header -->
                    <div class="text-center mb-4">
                        <h1 class="h3 text-primary fw-bold">FactuX</h1>
                        <p class="text-muted">Sistema de Facturación</p>
                    </div>

                    <!-- Mensajes de éxito -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Mensajes de error -->
                    @if($errors->has('login'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $errors->first('login') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Formulario de Login -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Campo Cédula -->
                        <div class="mb-3">
                            <label for="cedula" class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-1"></i>Cédula
                            </label>
                            <input type="text" 
                                   class="form-control @error('cedula') is-invalid @enderror" 
                                   id="cedula" 
                                   name="cedula" 
                                   value="{{ old('cedula') }}" 
                                   placeholder="Ingrese su cédula"
                                   required 
                                   autofocus>
                            @error('cedula')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Campo Contraseña -->
                        <div class="mb-3">
                            <label for="contraseña" class="form-label fw-semibold">
                                <i class="bi bi-lock me-1"></i>Contraseña
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control @error('contraseña') is-invalid @enderror" 
                                       id="contraseña" 
                                       name="contraseña" 
                                       placeholder="Ingrese su contraseña"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                                @error('contraseña')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Recordar sesión -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Recordar sesión
                            </label>
                        </div>

                        <!-- Botón de Login -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            © {{ date('Y') }} FactuX - Sistema de Facturación
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById('contraseña');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.className = 'bi bi-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleIcon.className = 'bi bi-eye';
    }
}
</script>

<style>
.min-vh-100 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
    border-radius: 15px;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b4c93 100%);
    transform: translateY(-1px);
}
</style>
@endsection