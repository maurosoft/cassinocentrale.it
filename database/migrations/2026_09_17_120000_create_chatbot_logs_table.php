<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->nullable()->index();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->string('provider', 40)->nullable();
            $table->string('model', 80)->nullable();
            $table->boolean('ok')->default(true);
            $table->string('error', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};
