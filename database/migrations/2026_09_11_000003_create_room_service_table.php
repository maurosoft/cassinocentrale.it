<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabella "ponte": collega le camere ai servizi (relazione molti-a-molti).
        Schema::create('room_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            // Costo extra specifico per questa camera (se diverso da quello del servizio).
            $table->decimal('extra_cost', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_service');
    }
};
