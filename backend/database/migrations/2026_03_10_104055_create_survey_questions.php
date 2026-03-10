<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->string('pregunta');
            $table->enum('tipo', ['texto', 'escala', 'opciones', 'multiple'])
                  ->default('escala');
            $table->json('opciones')->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('requerida')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};