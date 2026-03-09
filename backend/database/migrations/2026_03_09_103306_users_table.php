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
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido')->after('name')->nullable();
            $table->enum('rol', ['admin', 'mentor', 'alumno'])->default('alumno')->after('email');
            $table->unsignedBigInteger('department_id')->nullable()->after('rol');
            $table->string('cargo')->nullable()->after('department_id');
            $table->string('telefono', 20)->nullable()->after('cargo');
            $table->string('avatar')->nullable()->after('telefono');
            $table->boolean('activo')->default(true)->after('avatar');
            $table->timestamp('ultimo_acceso')->nullable()->after('activo');
            $table->timestamp('fecha_incorporacion')->nullable()->after('ultimo_acceso');
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('color', 7)->default('#3B82F6');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'apellido', 'rol', 'department_id', 'cargo',
                'telefono', 'avatar', 'activo', 'ultimo_acceso', 'fecha_incorporacion',
            ]);
        });
        Schema::dropIfExists('departments');
    }
};
