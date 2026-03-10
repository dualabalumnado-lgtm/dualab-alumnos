<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->timestamp('hora_entrada');
            $table->timestamp('hora_salida')->nullable();
            $table->string('ip_entrada', 45)->nullable();
            $table->string('ip_salida', 45)->nullable();
            $table->decimal('lat_entrada', 10, 8)->nullable();
            $table->decimal('lng_entrada', 11, 8)->nullable();
            $table->decimal('lat_salida', 10, 8)->nullable();
            $table->decimal('lng_salida', 11, 8)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichajes');
    }
};