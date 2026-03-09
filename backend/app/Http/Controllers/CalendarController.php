<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CalendarController extends Controller
{
    // GET /api/calendar/events
    public function index(Request $request): JsonResponse
    {
        $events = CalendarEvent::where(function ($q) use ($request) {
            $q->where('publico', true)
              ->orWhere('user_id', $request->user()->id);
        })
        ->when($request->filled('desde'), fn($q) => $q->where('fecha_inicio', '>=', $request->desde))
        ->when($request->filled('hasta'), fn($q) => $q->where('fecha_inicio', '<=', $request->hasta))
        ->orderBy('fecha_inicio')
        ->get();

        return response()->json($events);
    }

    // GET /api/calendar/events/{id}
    public function show(int $id): JsonResponse
    {
        return response()->json(CalendarEvent::findOrFail($id));
    }

    // POST /api/calendar/events
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:200',
            'descripcion'  => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'todo_el_dia'  => 'boolean',
            'color'        => 'nullable|string|size:7',
            'tipo'         => 'required|in:empresa,personal,formacion,reunion,festivo',
            'publico'      => 'boolean',
            'ubicacion'    => 'nullable|string|max:200',
            'url_reunion'  => 'nullable|url',
        ]);

        $event = CalendarEvent::create(array_merge($data, ['user_id' => $request->user()->id]));

        return response()->json($event, 201);
    }

    // PUT /api/calendar/events/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $event = CalendarEvent::findOrFail($id);

        $data = $request->validate([
            'titulo'       => 'sometimes|required|string|max:200',
            'descripcion'  => 'nullable|string',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin'    => 'nullable|date',
            'todo_el_dia'  => 'boolean',
            'color'        => 'nullable|string|size:7',
            'tipo'         => 'sometimes|required|in:empresa,personal,formacion,reunion,festivo',
            'publico'      => 'boolean',
            'ubicacion'    => 'nullable|string|max:200',
            'url_reunion'  => 'nullable|url',
        ]);

        $event->update($data);

        return response()->json($event->fresh());
    }

    // DELETE /api/calendar/events/{id}
    public function destroy(int $id): JsonResponse
    {
        CalendarEvent::findOrFail($id)->delete();

        return response()->json(['message' => 'Evento eliminado correctamente.']);
    }
}