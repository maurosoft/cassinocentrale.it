<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('guest_name');
            $table->unsignedTinyInteger('rating')->default(5); // 1-5 stelle
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->string('source')->nullable();  // es. Google, Booking, diretta
            $table->date('stay_date')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
