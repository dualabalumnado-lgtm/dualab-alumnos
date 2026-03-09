<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/auth/login
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if (! $user->activo) {
            return response()->json([
                'message' => 'Tu cuenta está desactivada. Contacta con el administrador.',
            ], 403);
        }

        // Revocar tokens anteriores si no pide "recordarme"
        if (! $request->boolean('remember')) {
            $user->tokens()->delete();
        }

        $expira = $request->boolean('remember') ? now()->addDays(30) : now()->addDay();
        $token  = $user->createToken('onboarding-spa', ['*'], $expira);

        $user->update(['ultimo_acceso' => now()]);

        return response()->json([
            'user'  => $user->load('department'),
            'token' => $token->plainTextToken,
        ]);
    }

    // GET /api/auth/user
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('department'));
    }

    // POST /api/auth/logout
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    // PUT /api/auth/profile
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name'     => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
        ]);

        $user->update($request->only(['name', 'apellido', 'telefono']));

        return response()->json([
            'message' => 'Perfil actualizado correctamente.',
            'user'    => $user->fresh()->load('department'),
        ]);
    }

    // PUT /api/auth/password
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual no es correcta.'],
            ]);
        }

        $user->update(['password' => Hash::make($request->password)]);
        $user->tokens()->delete(); // forzar re-login

        return response()->json(['message' => 'Contraseña actualizada. Por favor, vuelve a iniciar sesión.']);
    }
}