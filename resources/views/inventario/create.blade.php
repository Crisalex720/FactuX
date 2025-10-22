@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="container">
    <div class="header-section">
        <h1>Nuevo Producto</h1>
        <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="form-container">
        <form action="{{ route('inventario.store') }}" method="POST" class="product-form">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label for="codigo">Código del Producto*</label>
                    <input type="text" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="nombre">Nombre del Producto*</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="categoria_id">Categoría</label>
                    <select id="categoria_id" name="categoria_id">
                        <option value="">Selecciona una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio*</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0" value="{{ old('precio') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="stock">Stock Inicial*</label>
                    <input type="number" id="stock" name="stock" min="0" value="{{ old('stock') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                <a href="{{ route('inventario.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
