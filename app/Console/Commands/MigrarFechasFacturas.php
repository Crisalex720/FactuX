<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrarFechasFacturas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturas:migrar-fechas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra fechas para facturas existentes que no tienen fecha_factura';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de fechas para facturas existentes...');
        
        // Obtener facturas sin fecha_factura
        $facturasSinFecha = DB::table('factura')
            ->whereNull('fecha_factura')
            ->get();
            
        if ($facturasSinFecha->count() === 0) {
            $this->info('No hay facturas sin fecha para migrar.');
            return;
        }
        
        $this->info("Encontradas {$facturasSinFecha->count()} facturas sin fecha.");
        
        $fechaBase = Carbon::now('America/Bogota')->subDays(30); // Hace 30 días como referencia
        
        foreach ($facturasSinFecha as $factura) {
            // Asignar fecha basada en el consecutivo (simulando fechas progresivas)
            $fechaFactura = $fechaBase->copy()->addHours($factura->num_fact);
            
            DB::table('factura')
                ->where('id_fact', $factura->id_fact)
                ->update([
                    'fecha_factura' => $fechaFactura,
                    'created_at' => $fechaFactura,
                    'updated_at' => $fechaFactura
                ]);
                
            // También actualizar lista_prod relacionada
            DB::table('lista_prod')
                ->where('id_fact', $factura->id_fact)
                ->update([
                    'fecha_producto' => $fechaFactura,
                    'created_at' => $fechaFactura,
                    'updated_at' => $fechaFactura
                ]);
        }
        
        $this->info("✅ Migración completada. Se actualizaron {$facturasSinFecha->count()} facturas.");
        $this->info('Las fechas se asignaron basándose en el consecutivo de factura.');
    }
}
