<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->boolean('todo_el_dia')->default(false);
            $table->string('color', 7)->default('#3B82F6');
            $table->enum('tipo', ['empresa', 'personal', 'formacion', 'reunion', 'festivo'])
                  ->default('empresa');
            $table->boolean('publico')->default(true);
            $table->string('ubicacion')->nullable();
            $table->string('url_reunion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};