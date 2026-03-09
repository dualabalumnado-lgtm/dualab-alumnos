<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //Eventos de Calendario
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            $table->boolean('todo_el_dia')->default(false);
            $table->string('color', 7)->default('#3B82F6');
            $table->enum('tipo', ['empresa', 'personal', 'formacion', 'reunion', 'festivo'])->default('empresa');
            $table->boolean('publico')->default(true);
            $table->string('ubicacion')->nullable();
            $table->string('url_reunion')->nullable();
            $table->timestamps();
        });

        //Fichajes
        Schema::create('fichajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
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

        //Encuestas
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('pregunta');
            $table->enum('tipo', ['texto', 'escala', 'opciones', 'multiple'])->default('escala');
            $table->json('opciones')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('requerida')->default(true);
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('respuestas');
            $table->text('comentario_general')->nullable();
            $table->integer('puntuacion_general')->nullable();
            $table->timestamp('completada_en');
            $table->timestamps();
            $table->unique(['survey_id', 'user_id']);
        });

        //Historial Mentor IA
        Schema::create('mentor_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('mensaje_usuario');
            $table->text('respuesta_ia');
            $table->integer('tokens_usados')->default(0);
            $table->timestamps();
        });

        //Tareas
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completada', 'cancelada'])->default('pendiente');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('mentor_chats');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('fichajes');
        Schema::dropIfExists('calendar_events');
    }
};
