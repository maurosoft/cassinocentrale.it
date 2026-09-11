<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('number_name');            // es. "102", "205"
            $table->string('name')->nullable();        // nome descrittivo opzionale
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('rules')->nullable();         // regole check-in/out ecc.
            $table->decimal('base_price', 8, 2)->default(0);
            $table->unsignedTinyInteger('max_guests')->default(2);
            $table->boolean('has_kitchenette')->default(false);
            $table->json('images')->nullable();        // elenco percorsi foto
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
