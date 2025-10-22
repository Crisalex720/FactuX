<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $limite = $request->get('limite', 20);
        $busqueda = $request->get('busqueda', '');
        
        $query = DB::table('producto')->select('*');
        
        if (!empty($busqueda)) {
            $query->where(function($q) use ($busqueda) {
                $q->where('barcode', 'ILIKE', "%{$busqueda}%")
                  ->orWhere('nombre_prod', 'ILIKE', "%{$busqueda}%");
            });
        }
        
        $productos = $query->orderBy('id_producto', 'desc')
                          ->limit($limite)
                          ->get();
        
        return view('inventario.index', compact('productos', 'limite', 'busqueda'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_barra' => 'required|string',
            'nombre' => 'required|string',
            'cantidad' => 'required|integer|min:0',
            'precio_costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048|dimensions:max_width=500,max_height=500'
        ]);

        $imagen_url = '';
        
        // Manejo de imagen - usar ruta diferente
        if ($request->hasFile('imagen')) {
            $archivo = $request->file('imagen');
            $nombreArchivo = uniqid('prod_') . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path('uploads/productos'), $nombreArchivo);
            $imagen_url = 'uploads/productos/' . $nombreArchivo;
        }

        DB::table('producto')->insert([
            'barcode' => $request->codigo_barra,
            'nombre_prod' => $request->nombre,
            'cantidad_prod' => $request->cantidad,
            'precio_costop' => $request->precio_costo,
            'precio_ventap' => $request->precio_venta,
            'imagen_url' => $imagen_url
        ]);

        return redirect()->route('inventario.index')->with('success', 'Producto registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        // Normalizar datos antes de validar
        $cantidad = $request->cantidad;
        if ($cantidad === '' || $cantidad === null) {
            $cantidad = 0;
        }
        $request->merge(['cantidad' => (int)$cantidad]);

        $precio_costo = $request->precio_costo;
        if ($precio_costo === '' || $precio_costo === null) {
            $precio_costo = 0;
        }
        $request->merge(['precio_costo' => (float)$precio_costo]);

        $request->validate([
            'codigo_barra' => 'required|string',
            'nombre' => 'required|string',
            'cantidad' => 'integer|min:0',
            'precio_costo' => 'numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048|dimensions:max_width=500,max_height=500'
        ]);

        $producto = DB::table('producto')->where('id_producto', $id)->first();
        
        if (!$producto) {
            return redirect()->route('inventario.index')->with('error', 'Producto no encontrado.');
        }

        $imagen_url = $producto->imagen_url;

        // Eliminar imagen si se solicita
        if ($request->eliminar_imagen == '1' && !empty($imagen_url)) {
            $rutaCompleta = public_path($imagen_url);
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
            $imagen_url = '';
        }

        // Subir nueva imagen - usar ruta diferente
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if (!empty($imagen_url)) {
                $rutaCompleta = public_path($imagen_url);
                if (file_exists($rutaCompleta)) {
                    unlink($rutaCompleta);
                }
            }
            
            $archivo = $request->file('imagen');
            $nombreArchivo = uniqid('prod_') . '_' . $archivo->getClientOriginalName();
            $archivo->move(public_path('uploads/productos'), $nombreArchivo);
            $imagen_url = 'uploads/productos/' . $nombreArchivo;
        }

        DB::table('producto')->where('id_producto', $id)->update([
            'barcode' => $request->codigo_barra,
            'nombre_prod' => $request->nombre,
            'cantidad_prod' => $request->cantidad,
            'precio_costop' => $request->precio_costo,
            'precio_ventap' => $request->precio_venta,
            'imagen_url' => $imagen_url
        ]);

        return redirect()->route('inventario.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $producto = DB::table('producto')->where('id_producto', $id)->first();
        
        if (!$producto) {
            return redirect()->route('inventario.index')->with('error', 'Producto no encontrado.');
        }
        
        // Eliminar imagen si existe
        if (!empty($producto->imagen_url)) {
            $rutaCompleta = public_path($producto->imagen_url);
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
        }
        
        DB::table('producto')->where('id_producto', $id)->delete();
        
        return redirect()->route('inventario.index')->with('success', 'Producto eliminado correctamente.');
    }

    public function ajustarStock(Request $request)
    {
        $sumar = $request->input('sumar', []);
        $restar = $request->input('restar', []);

        foreach ($sumar as $id => $cantidad) {
            if ($cantidad > 0) {
                DB::table('producto')
                  ->where('id_producto', $id)
                  ->increment('cantidad_prod', $cantidad);
            }
        }

        foreach ($restar as $id => $cantidad) {
            if ($cantidad > 0) {
                DB::table('producto')
                  ->where('id_producto', $id)
                  ->decrement('cantidad_prod', $cantidad);
            }
        }

        return redirect()->route('inventario.index')->with('success', 'Stock de productos actualizado correctamente.');
    }
}
