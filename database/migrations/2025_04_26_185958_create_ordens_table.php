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
        Schema::create('ordens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usuario_id'); // Comprador
            $table->decimal('total', 10, 2);
            $table->enum('estado', [
                'pendiente',     // Estado inicial
                'validada',      // Validada por un gerente
                'pagado',        // Pago confirmado
                'enviado',       // En camino
                'entregado',     // Entregada al cliente
                'cancelado'      // Cancelada
            ])->default('pendiente');
            $table->string('ticket_path')->nullable(); // ¡Campo nuevo para el comprobante bancario!
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordens');
    }
};
