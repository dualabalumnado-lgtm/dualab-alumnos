<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MentorController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API Dualab-alumnos funcionando'
    ]);
});

Route::post('/mentor', [MentorController::class, 'chat']);