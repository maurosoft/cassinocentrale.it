<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // etichetta libera (es. "UoZap principale")
            $table->string('provider');                   // uozap | hooki (spotwab = hooki)
            $table->string('base_url')->nullable();
            $table->text('token')->nullable();            // cifrato dal cast
            $table->string('instance_id')->nullable();    // gateway_identifier (UoZap) / sessionId (Hooki)
            $table->string('sender')->nullable();
            $table->unsignedSmallInteger('timeout')->default(15);
            $table->unsignedInteger('order_fallback')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->nullable();
            $table->string('to')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('sent');    // sent | failed
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_providers');
        Schema::dropIfExists('whatsapp_logs');
    }
};
