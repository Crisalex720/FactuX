<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("CREATE VIEW \"vista_listado_facturas\" AS SELECT f.id_fact,
    f.num_fact AS consecutivo,
    f.prefijo_fact AS prefijo,
    c.nombre_cl AS nombre_cliente,
    t.nombre AS atendido_por,
    f.estado,
    ( SELECT string_agg(((((p.nombre_prod)::text || ' ('::text) || lp.cantidad) || ')'::text), ', '::text ORDER BY p.nombre_prod) AS string_agg
           FROM (lista_prod lp
             JOIN producto p ON ((lp.id_producto = p.id_producto)))
          WHERE (lp.id_fact = f.id_fact)) AS productos,
    ( SELECT sum((lp.cantidad * p.precio_ventap)) AS sum
           FROM (lista_prod lp
             JOIN producto p ON ((lp.id_producto = p.id_producto)))
          WHERE (lp.id_fact = f.id_fact)) AS total_factura
   FROM ((factura f
     JOIN cliente c ON ((f.cliente = c.id_cliente)))
     JOIN trabajadores t ON ((f.id_trab = t.id_trab)));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS \"vista_listado_facturas\"");
    }
};
