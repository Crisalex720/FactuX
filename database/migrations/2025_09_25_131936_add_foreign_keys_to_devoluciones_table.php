<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->foreign(['id_fact'], 'devoluciones_id_fact_fkey')->references(['id_fact'])->on('factura')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_lista'], 'devoluciones_id_lista_fkey')->references(['id_lista'])->on('lista_prod')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_prod'], 'devoluciones_id_prod_fkey')->references(['id_producto'])->on('producto')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->dropForeign('devoluciones_id_fact_fkey');
            $table->dropForeign('devoluciones_id_lista_fkey');
            $table->dropForeign('devoluciones_id_prod_fkey');
        });
    }
};
