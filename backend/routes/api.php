<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\FichajeController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes — OnboardingPro
|--------------------------------------------------------------------------
*/

//Rutas públicas
Route::prefix('auth')->group(function () {
    Route::post('/login',          [AuthController::class, 'login']);
    Route::post('/forgot-password',[AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
});

//Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/user',         [AuthController::class, 'user']);
        Route::post('/logout',      [AuthController::class, 'logout']);
        Route::put('/profile',      [AuthController::class, 'updateProfile']);
        Route::put('/password',     [AuthController::class, 'updatePassword']);
    });

    //Calendario
    Route::prefix('calendar')->group(function () {
        Route::get('/events',       [CalendarController::class, 'index']);
        Route::get('/events/{id}',  [CalendarController::class, 'show']);
        Route::post('/events',      [CalendarController::class, 'store'])->middleware('role:admin,mentor');
        Route::put('/events/{id}',  [CalendarController::class, 'update'])->middleware('role:admin,mentor');
        Route::delete('/events/{id}',[CalendarController::class, 'destroy'])->middleware('role:admin,mentor');
    });

    //Fichaje
    Route::prefix('fichaje')->group(function () {
        Route::get('/hoy',              [FichajeController::class, 'hoy']);
        Route::get('/historial',        [FichajeController::class, 'historial']);
        Route::get('/resumen-semana',   [FichajeController::class, 'resumenSemana']);
        Route::post('/entrada',         [FichajeController::class, 'entrada']);
        Route::post('/salida',          [FichajeController::class, 'salida']);
        // Solo admin/mentor
        Route::get('/todos',            [FichajeController::class, 'todos'])->middleware('role:admin,mentor');
        Route::get('/usuario/{userId}', [FichajeController::class, 'porUsuario'])->middleware('role:admin,mentor');
    });

    //Encuesta
    Route::prefix('survey')->group(function () {
        Route::get('/',              [SurveyController::class, 'index']);
        Route::get('/preguntas',     [SurveyController::class, 'preguntas']);
        Route::post('/responder',    [SurveyController::class, 'responder']);
        Route::get('/mi-respuesta',  [SurveyController::class, 'miRespuesta']);
        // Solo admin
        Route::get('/resultados',    [SurveyController::class, 'resultados'])->middleware('role:admin');
        Route::get('/estadisticas',  [SurveyController::class, 'estadisticas'])->middleware('role:admin');
        Route::post('/preguntas',    [SurveyController::class, 'crearPregunta'])->middleware('role:admin');
    });

    //Mentor Virtual IA
    Route::prefix('mentor')->group(function () {
        Route::post('/chat',         [MentorController::class, 'chat']);
        Route::get('/historial',     [MentorController::class, 'historial']);
        Route::delete('/historial',  [MentorController::class, 'limpiarHistorial']);
    });

    //Tareas
    Route::prefix('tasks')->group(function () {
        Route::get('/',                      [TaskController::class, 'index']);
        Route::post('/',                     [TaskController::class, 'store']);
        Route::get('/{id}',                  [TaskController::class, 'show']);
        Route::put('/{id}',                  [TaskController::class, 'update']);
        Route::delete('/{id}',               [TaskController::class, 'destroy']);
        Route::patch('/{id}/estado',         [TaskController::class, 'cambiarEstado']);
        Route::post('/generar-ia',           [TaskController::class, 'generarConIA']);
        Route::post('/generar-multiples',    [TaskController::class, 'generarMultiplesConIA']);
    });

    //Usuarios (solo admin)
    Route::middleware('role:admin')->prefix('users')->group(function () {
        Route::get('/',      [UserController::class, 'index']);
        Route::post('/',     [UserController::class, 'store']);
        Route::get('/{id}',  [UserController::class, 'show']);
        Route::put('/{id}',  [UserController::class, 'update']);
        Route::delete('/{id}',[UserController::class, 'destroy']);
    });

});