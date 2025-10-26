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
        Schema::create('caja', function (Blueprint $table) {
            $table->id('id_caja');
            $table->decimal('dinero_base', 10, 2)->comment('Dinero inicial con el que se abre la caja');
            $table->decimal('dinero_contado', 10, 2)->nullable()->comment('Dinero contado al cierre (incluye base)');
            $table->decimal('total_ventas', 10, 2)->default(0)->comment('Total de ventas del período');
            $table->decimal('diferencia', 10, 2)->default(0)->comment('Diferencia entre contado y esperado');
            $table->datetime('fecha_apertura')->comment('Fecha y hora de apertura');
            $table->datetime('fecha_cierre')->nullable()->comment('Fecha y hora de cierre');
            $table->enum('tipo_cierre', ['diario', 'personalizado'])->default('diario');
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->unsignedBigInteger('id_trab_apertura')->comment('Trabajador que abrió la caja');
            $table->unsignedBigInteger('id_trab_cierre')->nullable()->comment('Trabajador que cerró la caja');
            $table->text('observaciones')->nullable()->comment('Observaciones del cierre');
            $table->timestamps();

            // Índices y relaciones
            $table->foreign('id_trab_apertura')->references('id_trab')->on('trabajadores');
            $table->foreign('id_trab_cierre')->references('id_trab')->on('trabajadores');
            $table->index(['fecha_apertura', 'fecha_cierre']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja');
    }
};
