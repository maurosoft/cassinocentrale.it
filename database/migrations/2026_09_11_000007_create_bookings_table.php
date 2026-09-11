<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->nullable(); // codice prenotazione
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedTinyInteger('number_of_guests')->default(1);
            $table->text('notes')->nullable();            // note dell'ospite
            $table->text('internal_notes')->nullable();   // note interne dello staff
            $table->string('status')->default('draft');   // draft/confirmed/cancelled/completed
            $table->decimal('total_price', 10, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->string('payment_status')->default('unpaid'); // unpaid/paid/refunded
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamps();

            $table->index(['check_in', 'check_out']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
