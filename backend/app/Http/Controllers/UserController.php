<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET /api/users
    public function index(Request $request): JsonResponse
    {
        $users = User::with('department')
            ->when($request->filled('rol'), fn($q) => $q->where('rol', $request->rol))
            ->when($request->filled('activo'), fn($q) => $q->where('activo', $request->boolean('activo')))
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }

    // POST /api/users
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                => 'required|string|max:100',
            'apellido'            => 'nullable|string|max:100',
            'email'               => 'required|email|unique:users,email',
            'password'            => 'required|string|min:8',
            'rol'                 => 'required|in:admin,mentor,alumno',
            'department_id'       => 'nullable|exists:departments,id',
            'cargo'               => 'nullable|string|max:100',
            'fecha_incorporacion' => 'nullable|date',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json($user->load('department'), 201);
    }

    // GET /api/users/{id}
    public function show(int $id): JsonResponse
    {
        return response()->json(User::with('department')->findOrFail($id));
    }

    // PUT /api/users/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:100',
            'apellido'      => 'nullable|string|max:100',
            'email'         => 'sometimes|required|email|unique:users,email,' . $id,
            'rol'           => 'sometimes|required|in:admin,mentor,alumno',
            'department_id' => 'nullable|exists:departments,id',
            'cargo'         => 'nullable|string|max:100',
            'activo'        => 'boolean',
        ]);

        $user->update($data);

        return response()->json($user->fresh()->load('department'));
    }

    // DELETE /api/users/{id}
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }
}