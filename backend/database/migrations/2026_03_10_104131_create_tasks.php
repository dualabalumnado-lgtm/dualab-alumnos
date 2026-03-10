<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'cancelada'])
                  ->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])
                  ->default('media');
            $table->string('categoria')->default('otro');
            $table->decimal('tiempo_estimado', 5, 2)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->json('subtareas')->nullable();
            $table->json('etiquetas')->nullable();
            $table->boolean('generada_con_ia')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamp('completada_en')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};