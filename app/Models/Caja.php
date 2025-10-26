<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Caja extends Model
{
    protected $table = 'caja';
    protected $primaryKey = 'id_caja';

    protected $fillable = [
        'dinero_base',
        'dinero_contado',
        'total_ventas',
        'diferencia',
        'fecha_apertura',
        'fecha_cierre',
        'tipo_cierre',
        'estado',
        'id_trab_apertura',
        'id_trab_cierre',
        'observaciones'
    ];

    protected $casts = [
        'dinero_base' => 'decimal:2',
        'dinero_contado' => 'decimal:2',
        'total_ventas' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    // Relaciones
    public function trabajadorApertura(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'id_trab_apertura', 'id_trab');
    }

    public function trabajadorCierre(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'id_trab_cierre', 'id_trab');
    }

    // Scopes
    public function scopeAbiertas($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeCerradas($query)
    {
        return $query->where('estado', 'cerrada');
    }

    // Métodos auxiliares
    public function calcularTotalVentas()
    {
        $fechaInicio = $this->fecha_apertura;
        $fechaFin = $this->fecha_cierre ?? now();

        // Si es cierre diario, ajustar las horas
        if ($this->tipo_cierre === 'diario') {
            $fechaInicio = Carbon::parse($this->fecha_apertura)->setTime(6, 0, 0);
            $fechaFin = $fechaInicio->copy()->addDay()->setTime(6, 0, 0);
        }

        return DB::table('factura as f')
            ->join('lista_prod as lp', 'f.id_fact', '=', 'lp.id_fact')
            ->join('producto as p', 'lp.id_producto', '=', 'p.id_producto')
            ->where('f.estado', 'activa')
            ->whereBetween('f.created_at', [$fechaInicio, $fechaFin])
            ->sum(DB::raw('lp.cantidad * p.precio_ventap'));
    }

    public function getFacturasDelPeriodo()
    {
        $fechaInicio = $this->fecha_apertura;
        $fechaFin = $this->fecha_cierre ?? now();

        // Si es cierre diario, ajustar las horas
        if ($this->tipo_cierre === 'diario') {
            $fechaInicio = Carbon::parse($this->fecha_apertura)->setTime(6, 0, 0);
            $fechaFin = $fechaInicio->copy()->addDay()->setTime(6, 0, 0);
        }

        return DB::table('vista_listado_facturas')
            ->where('estado', 'activa')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('consecutivo', 'desc')
            ->get();
    }

    public function calcularDiferencia()
    {
        if ($this->dinero_contado !== null) {
            $dineroEsperado = $this->dinero_base + $this->total_ventas;
            $this->diferencia = $this->dinero_contado - $dineroEsperado;
            return $this->diferencia;
        }
        return 0;
    }

    // Verificar si hay una caja abierta
    public static function hayAbierta()
    {
        return self::where('estado', 'abierta')->exists();
    }

    // Obtener la caja abierta actual
    public static function obtenerAbierta()
    {
        return self::where('estado', 'abierta')->first();
    }
}
