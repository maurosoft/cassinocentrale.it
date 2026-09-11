<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable();        // storia, arte, natura, guerra...
            $table->string('type')->nullable();             // per le attività locali: negozio, ristorante, bar...
            $table->string('distance_walking')->nullable(); // es. "10 min a piedi"
            $table->string('distance_car')->nullable();
            $table->string('distance_bus')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('link')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_convention')->default(false); // attività convenzionata?
            $table->text('convention_description')->nullable(); // dettaglio offerta/sconto
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_convention', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
