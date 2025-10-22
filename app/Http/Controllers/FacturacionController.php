<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Trabajador;

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
        
        // Obtener facturas con productos
        $facturas = DB::table('factura as f')
            ->leftJoin('cliente as c', 'f.cliente', '=', 'c.id_cliente')
            ->leftJoin('trabajadores as t', 'f.id_trab', '=', 't.id_trab')
            ->select([
                'f.*',
                'c.nombre_cl as nombre_cliente',
                't.nombre as atendido_por',
                DB::raw('COALESCE(f.prefijo_fact, \'FACT\') as prefijo_fact')
            ])
            ->orderBy('f.num_fact', 'desc')
            ->get()
            ->map(function ($factura) {
                // Calcular total y productos
                $productos = DB::table('lista_prod as lp')
                    ->join('producto as p', 'lp.id_producto', '=', 'p.id_producto')
                    ->where('lp.id_fact', $factura->id_fact)
                    ->select('p.nombre_prod', 'lp.cantidad', 'p.precio_ventap')
                    ->get();
                
                $total = $productos->sum(function ($p) {
                    return $p->precio_ventap * $p->cantidad;
                });
                
                $productosDetalle = $productos->map(function ($p) {
                    return $p->nombre_prod . ' (' . $p->cantidad . ')';
                })->implode(', ');
                
                $factura->total_factura = $total;
                $factura->productos_detalle = $productosDetalle;
                
                return $factura;
            });
        
        return view('facturacion.index', compact(
            'productos', 
            'clientes', 
            'clienteDefault', 
            'nextConsecutivo', 
            'facturas'
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

            // Crear factura
            $factura = Factura::create([
                'cliente' => $request->id_cliente,
                'id_trab' => $trabajador->id_trab,
                'estado' => 'activa',
                'num_fact' => $nextConsecutivo,
                'prefijo_fact' => 'FACT'
            ]);

            // Agregar productos a la factura
            foreach ($carrito as $item) {
                DB::table('lista_prod')->insert([
                    'id_fact' => $factura->id_fact,
                    'id_producto' => $item['id_producto'],
                    'cantidad' => $item['cantidad']
                ]);
            }

            // Limpiar carrito
            Session::forget('carrito');

            DB::commit();

            return redirect()->route('facturacion.index')->with('success', 'Factura registrada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('facturacion.index')->with('error', 'Error al registrar la factura: ' . $e->getMessage());
        }
    }

    public function anularFactura($idFactura)
    {
        $factura = Factura::findOrFail($idFactura);
        $factura->update(['estado' => 'anulado']);
        
        return redirect()->route('facturacion.index')->with('success', 'Factura anulada correctamente');
    }
}
