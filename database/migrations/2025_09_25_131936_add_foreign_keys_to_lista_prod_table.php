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
        Schema::table('lista_prod', function (Blueprint $table) {
            $table->foreign(['id_fact'], 'lista_prod_id_fact_fkey')->references(['id_fact'])->on('factura')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_producto'], 'lista_prod_id_producto_fkey')->references(['id_producto'])->on('producto')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lista_prod', function (Blueprint $table) {
            $table->dropForeign('lista_prod_id_fact_fkey');
            $table->dropForeign('lista_prod_id_producto_fkey');
        });
    }
};
