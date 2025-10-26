<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Trabajador;
use Carbon\Carbon;

class FacturacionController extends Controller
{
    public function index()
    {
        // Obtener cliente final por defecto
        $clienteDefault = Cliente::where('nombre_cl', 'ilike', '%cliente final%')->first();
        
        // Obtener productos y clientes
        $productos = Producto::orderBy('nombre_prod')->get();
        $clientes = Cliente::orderBy('nombre_cl')->get();
        
        // Obtener próximo consecutivo
        $nextConsecutivo = Factura::max('num_fact') + 1;
        
        return view('facturacion.index', compact(
            'productos', 
            'clientes', 
            'clienteDefault', 
            'nextConsecutivo'
        ));
    }

    public function agregarProducto(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:producto,id_producto',
            'cantidad' => 'required|integer|min:1'
        ]);

        $carrito = Session::get('carrito', []);
        $idProducto = $request->id_producto;
        $cantidad = $request->cantidad;

        // Verificar si el producto ya está en el carrito
        $encontrado = false;
        foreach ($carrito as &$item) {
            if ($item['id_producto'] == $idProducto) {
                $item['cantidad'] += $cantidad;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $carrito[] = [
                'id_producto' => $idProducto,
                'cantidad' => $cantidad
            ];
        }

        Session::put('carrito', $carrito);
        
        return redirect()->route('facturacion.index')->with('success', 'Producto agregado al carrito');
    }

    public function quitarProducto($idProducto)
    {
        $carrito = Session::get('carrito', []);
        $carrito = array_filter($carrito, function($item) use ($idProducto) {
            return $item['id_producto'] != $idProducto;
        });
        
        Session::put('carrito', array_values($carrito));
        
        return redirect()->route('facturacion.index')->with('success', 'Producto removido del carrito');
    }

    public function agregarProductoAjax(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:producto,id_producto',
            'cantidad' => 'required|integer|min:1'
        ]);

        $carrito = Session::get('carrito', []);
        $idProducto = $request->id_producto;
        $cantidad = $request->cantidad;

        // Verificar si el producto ya está en el carrito
        $encontrado = false;
        foreach ($carrito as &$item) {
            if ($item['id_producto'] == $idProducto) {
                $item['cantidad'] += $cantidad;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $carrito[] = [
                'id_producto' => $idProducto,
                'cantidad' => $cantidad
            ];
        }

        Session::put('carrito', $carrito);
        
        return response()->json([
            'success' => true,
            'carrito' => $this->formatearCarrito($carrito)
        ]);
    }

    public function quitarProductoAjax($idProducto)
    {
        $carrito = Session::get('carrito', []);
        $carrito = array_filter($carrito, function($item) use ($idProducto) {
            return $item['id_producto'] != $idProducto;
        });
        
        Session::put('carrito', array_values($carrito));
        
        return response()->json([
            'success' => true,
            'carrito' => $this->formatearCarrito($carrito)
        ]);
    }

    public function obtenerCarrito()
    {
        $carrito = Session::get('carrito', []);
        return response()->json([
            'carrito' => $this->formatearCarrito($carrito)
        ]);
    }

    private function formatearCarrito($carrito)
    {
        $productos = Producto::whereIn('id_producto', collect($carrito)->pluck('id_producto'))->get();
        $carritoFormateado = [];
        $totalFactura = 0;

        foreach ($carrito as $item) {
            $producto = $productos->firstWhere('id_producto', $item['id_producto']);
            if ($producto) {
                $precio = $producto->precio_ventap;
                $subtotal = $precio * $item['cantidad'];
                $totalFactura += $subtotal;

                $carritoFormateado[] = [
                    'id_producto' => $item['id_producto'],
                    'nombre_prod' => $producto->nombre_prod,
                    'precio' => $precio,
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $subtotal,
                    'iva_porcentaje' => $producto->iva_porcentaje ?? 0,
                    'valor_iva' => $producto->valor_iva ?? 0
                ];
            }
        }

        return [
            'items' => $carritoFormateado,
            'total' => $totalFactura,
            'nextConsecutivo' => Factura::max('num_fact') + 1
        ];
    }

    public function finalizarFactura(Request $request)
    {
        $carrito = Session::get('carrito', []);
        
        if (empty($carrito)) {
            return redirect()->route('facturacion.index')->with('error', 'El carrito está vacío');
        }

        $request->validate([
            'id_cliente' => 'required|exists:cliente,id_cliente'
        ]);

        try {
            DB::beginTransaction();

            // Obtener trabajador (aquí deberías usar el sistema de autenticación de Laravel)
            $trabajador = Trabajador::first(); // Temporal - usar auth()->user() cuando implementes autenticación
            
            // Obtener próximo consecutivo
            $nextConsecutivo = Factura::max('num_fact') + 1;

            // Crear factura con timestamp de Bogotá
            $fechaBogota = Carbon::now('America/Bogota');
            $factura = Factura::create([
                'cliente' => $request->id_cliente,
                'id_trab' => $trabajador->id_trab,
                'estado' => 'activa',
                'num_fact' => $nextConsecutivo,
                'prefijo_fact' => 'FACT',
                'fecha_factura' => $fechaBogota
            ]);

            // Agregar productos a la factura con timestamps de Bogotá
            foreach ($carrito as $item) {
                DB::table('lista_prod')->insert([
                    'id_fact' => $factura->id_fact,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad'],
                    'fecha_producto' => $fechaBogota,
                    'created_at' => $fechaBogota,
                    'updated_at' => $fechaBogota
                ]);
            }

            // Limpiar carrito
            Session::forget('carrito');

            DB::commit();

            // Restablecer cliente final después de crear la factura
            $clienteDefault = Cliente::where('nombre_cl', 'ilike', '%cliente final%')->first();
            
            // Redirigir a la tirilla POS para impresión automática
            return redirect()->route('facturacion.tirilla', ['id' => $factura->id_fact, 'print' => 'true']);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('facturacion.index')->with('error', 'Error al registrar la factura: ' . $e->getMessage());
        }
    }

    public function tirillaPOS($idFactura)
    {
        try {
            // Obtener factura de la tabla principal (más confiable)
            $factura = DB::table('factura')->where('id_fact', $idFactura)->first();
            
            if (!$factura) {
                return redirect()->route('facturacion.index')->with('error', 'Factura no encontrada');
            }

            // Agregar campos calculados para compatibilidad con la vista
            $factura->prefijo = $factura->prefijo_fact ?? 'FACT';
            $factura->consecutivo = $factura->num_fact ?? $factura->id_fact;

            $productos = DB::table('lista_prod as lp')
                ->join('producto as p', 'lp.id_producto', '=', 'p.id_producto')
                ->where('lp.id_fact', $idFactura)
                ->select('p.nombre_prod', 'lp.cantidad', 'p.precio_ventap', 
                         'p.iva_porcentaje', 'p.valor_iva',
                         DB::raw('lp.cantidad * p.precio_ventap as subtotal'))
                ->get();

            // Obtener cliente
            $cliente = null;
            if ($factura->cliente) {
                $cliente = DB::table('cliente')->where('id_cliente', $factura->cliente)->first();
            }

            // Obtener trabajador
            $trabajador = null;
            if ($factura->id_trab) {
                $trabajador = DB::table('trabajadores')->where('id_trab', $factura->id_trab)->first();
            }

            return view('facturacion.tirilla-pos', compact('factura', 'productos', 'cliente', 'trabajador'));
            
        } catch (\Exception $e) {
            // En caso de error, mostrar más información para debugging
            Log::error('Error en tirillaPOS: ' . $e->getMessage() . ' - Línea: ' . $e->getLine() . ' - Archivo: ' . $e->getFile());
            return redirect()->route('facturacion.index')->with('error', 'Error al cargar la tirilla: ' . $e->getMessage());
        }
    }
}
