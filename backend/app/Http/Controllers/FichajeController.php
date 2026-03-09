<?php

namespace App\Http\Controllers;

use App\Models\Fichaje;
use App\Services\FichajeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FichajeController extends Controller
{
    public function __construct(protected FichajeService $service) {}

    // GET /api/fichaje/hoy
    public function hoy(Request $request): JsonResponse
    {
        $fichaje = Fichaje::where('user_id', $request->user()->id)
            ->whereDate('fecha', today())
            ->first();

        return response()->json([
            'fichaje'          => $fichaje,
            'estado'           => $this->service->getEstado($fichaje),
            'horas_trabajadas' => $fichaje ? $this->service->calcularHoras($fichaje) : null,
        ]);
    }

    // POST /api/fichaje/entrada
    public function entrada(Request $request): JsonResponse
    {
        $user = $request->user();

        $existente = Fichaje::where('user_id', $user->id)
            ->whereDate('fecha', today())
            ->first();

        if ($existente) {
            return response()->json([
                'message' => 'Ya has registrado tu entrada hoy.',
                'fichaje' => $existente,
            ], 422);
        }

        $fichaje = Fichaje::create([
            'user_id'     => $user->id,
            'fecha'       => today(),
            'hora_entrada'=> now(),
            'ip_entrada'  => $request->ip(),
            'lat_entrada' => $request->input('lat'),
            'lng_entrada' => $request->input('lng'),
        ]);

        return response()->json([
            'message' => '¡Buenos días! Entrada registrada a las ' . now()->format('H:i') . 'h.',
            'fichaje' => $fichaje,
        ], 201);
    }

    // POST /api/fichaje/salida
    public function salida(Request $request): JsonResponse
    {
        $fichaje = Fichaje::where('user_id', $request->user()->id)
            ->whereDate('fecha', today())
            ->whereNull('hora_salida')
            ->first();

        if (! $fichaje) {
            return response()->json([
                'message' => 'No tienes una entrada activa hoy o ya registraste la salida.',
            ], 422);
        }

        $fichaje->update([
            'hora_salida' => now(),
            'ip_salida'   => $request->ip(),
            'lat_salida'  => $request->input('lat'),
            'lng_salida'  => $request->input('lng'),
        ]);

        $horas = $this->service->calcularHoras($fichaje->fresh());

        return response()->json([
            'message'          => "¡Hasta mañana! Salida registrada. Has trabajado {$horas['formato']}h hoy.",
            'fichaje'          => $fichaje->fresh(),
            'horas_trabajadas' => $horas,
        ]);
    }

    // GET /api/fichaje/historial
    public function historial(Request $request): JsonResponse
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
            'mes'   => 'nullable|integer|min:1|max:12',
            'anio'  => 'nullable|integer|min:2020',
        ]);

        $query = Fichaje::where('user_id', $request->user()->id);

        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->whereBetween('fecha', [$request->desde, $request->hasta]);
        } elseif ($request->filled('mes') && $request->filled('anio')) {
            $query->whereYear('fecha', $request->anio)->whereMonth('fecha', $request->mes);
        } else {
            $query->where('fecha', '>=', now()->subDays(30));
        }

        $fichajes = $query->orderBy('fecha', 'desc')->get()->map(fn($f) => array_merge(
            $f->toArray(),
            ['horas_trabajadas' => $this->service->calcularHoras($f)]
        ));

        return response()->json([
            'fichajes'        => $fichajes,
            'total_horas'     => $this->service->totalHoras($fichajes),
            'dias_trabajados' => $fichajes->count(),
        ]);
    }

    // GET /api/fichaje/resumen-semana
    public function resumenSemana(Request $request): JsonResponse
    {
        $inicio   = now()->startOfWeek();
        $fin      = now()->endOfWeek();

        $fichajes = Fichaje::where('user_id', $request->user()->id)
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderBy('fecha')
            ->get()
            ->map(fn($f) => array_merge(
                $f->toArray(),
                ['horas' => $this->service->calcularHoras($f)]
            ));

        return response()->json([
            'semana'         => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
            'fichajes'       => $fichajes,
            'total_horas'    => $this->service->totalHoras($fichajes),
            'objetivo_horas' => 40,
        ]);
    }

    // GET /api/fichaje/usuario/{userId}  — admin/mentor
    public function porUsuario(int $userId): JsonResponse
    {
        $fichajes = Fichaje::where('user_id', $userId)
            ->orderBy('fecha', 'desc')
            ->limit(30)
            ->get();

        return response()->json($fichajes);
    }

    // GET /api/fichaje/todos  — admin/mentor
    public function todos(): JsonResponse
    {
        $fichajes = Fichaje::with('user:id,name,apellido,email,avatar')
            ->whereDate('fecha', today())
            ->orderBy('hora_entrada', 'desc')
            ->get();

        return response()->json($fichajes);
    }
}