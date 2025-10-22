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
        Schema::table('factura', function (Blueprint $table) {
            $table->foreign(['cliente'], 'factura_cliente_fkey')->references(['id_cliente'])->on('cliente')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_trab'], 'factura_id_trab_fkey')->references(['id_trab'])->on('trabajadores')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura', function (Blueprint $table) {
            $table->dropForeign('factura_cliente_fkey');
            $table->dropForeign('factura_id_trab_fkey');
        });
    }
};
