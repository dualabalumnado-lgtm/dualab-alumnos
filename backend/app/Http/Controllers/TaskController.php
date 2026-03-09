<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\ClaudeAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(protected ClaudeAIService $ai) {}

    // GET /api/tasks
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::where('user_id', $request->user()->id)
            ->orderBy('orden')
            ->orderByRaw("FIELD(prioridad, 'urgente', 'alta', 'media', 'baja')")
            ->get();

        return response()->json($tasks);
    }

    // POST /api/tasks
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo'            => 'required|string|max:80',
            'descripcion'       => 'nullable|string',
            'prioridad'         => 'required|in:baja,media,alta,urgente',
            'categoria'         => 'required|string',
            'tiempo_estimado'   => 'nullable|numeric|min:0.5',
            'fecha_vencimiento' => 'nullable|date',
            'subtareas'         => 'nullable|array',
            'etiquetas'         => 'nullable|array',
        ]);

        $task = Task::create(array_merge($data, [
            'user_id' => $request->user()->id,
            'estado'  => 'pendiente',
        ]));

        return response()->json($task, 201);
    }

    // GET /api/tasks/{id}
    public function show(Request $request, int $id): JsonResponse
    {
        return response()->json(
            Task::where('user_id', $request->user()->id)->findOrFail($id)
        );
    }

    // PUT /api/tasks/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        $task->update($request->only([
            'titulo', 'descripcion', 'prioridad', 'categoria',
            'tiempo_estimado', 'fecha_vencimiento',
            'subtareas', 'etiquetas', 'estado', 'orden',
        ]));

        return response()->json($task->fresh());
    }

    // DELETE /api/tasks/{id}
    public function destroy(Request $request, int $id): JsonResponse
    {
        Task::where('user_id', $request->user()->id)->findOrFail($id)->delete();

        return response()->json(['message' => 'Tarea eliminada.']);
    }

    // PATCH /api/tasks/{id}/estado
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completada,cancelada',
        ]);

        $task = Task::where('user_id', $request->user()->id)->findOrFail($id);

        $task->update([
            'estado'       => $request->estado,
            'completada_en'=> $request->estado === 'completada' ? now() : null,
        ]);

        return response()->json($task->fresh());
    }

    // POST /api/tasks/generar-ia
    public function generarConIA(Request $request): JsonResponse
    {
        $request->validate([
            'descripcion' => 'required|string|max:500',
            'guardar'     => 'boolean',
        ]);

        try {
            $tareaGenerada = $this->ai->generarTarea($request->descripcion);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        if (isset($tareaGenerada['error'])) {
            return response()->json(['message' => $tareaGenerada['error']], 500);
        }

        if ($request->boolean('guardar')) {
            $task = Task::create([
                'user_id'           => $request->user()->id,
                'titulo'            => $tareaGenerada['titulo'],
                'descripcion'       => $tareaGenerada['descripcion'] ?? '',
                'prioridad'         => $tareaGenerada['prioridad'] ?? 'media',
                'categoria'         => $tareaGenerada['categoria'] ?? 'otro',
                'tiempo_estimado'   => $tareaGenerada['tiempo_estimado'] ?? null,
                'subtareas'         => $tareaGenerada['subtareas'] ?? [],
                'etiquetas'         => $tareaGenerada['etiquetas'] ?? [],
                'generada_con_ia'   => true,
                'estado'            => 'pendiente',
            ]);

            return response()->json(['tarea_generada' => $tareaGenerada, 'tarea_guardada' => $task], 201);
        }

        return response()->json(['tarea_generada' => $tareaGenerada]);
    }

    // POST /api/tasks/generar-multiples
    public function generarMultiplesConIA(Request $request): JsonResponse
    {
        $request->validate([
            'objetivo' => 'required|string|max:500',
            'cantidad' => 'nullable|integer|min:2|max:10',
            'guardar'  => 'boolean',
        ]);

        try {
            $tareas = $this->ai->generarMultiplesTareas(
                $request->objetivo,
                $request->integer('cantidad', 5)
            );
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        if ($request->boolean('guardar')) {
            $creadas = [];
            foreach ($tareas as $t) {
                $creadas[] = Task::create([
                    'user_id'         => $request->user()->id,
                    'titulo'          => $t['titulo'],
                    'descripcion'     => $t['descripcion'] ?? '',
                    'prioridad'       => $t['prioridad'] ?? 'media',
                    'categoria'       => $t['categoria'] ?? 'otro',
                    'tiempo_estimado' => $t['tiempo_estimado'] ?? null,
                    'orden'           => $t['orden'] ?? 0,
                    'generada_con_ia' => true,
                    'estado'          => 'pendiente',
                ]);
            }
            return response()->json(['tareas_creadas' => $creadas], 201);
        }

        return response()->json(['tareas_generadas' => $tareas]);
    }
}