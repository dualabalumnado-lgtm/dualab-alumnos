<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SurveyController extends Controller
{
    // GET /api/survey
    public function index(): JsonResponse
    {
        return response()->json(Survey::where('activa', true)->latest()->first());
    }

    // GET /api/survey/preguntas
    public function preguntas(): JsonResponse
    {
        $survey = Survey::where('activa', true)->with('questions')->latest()->first();

        return response()->json($survey?->questions ?? []);
    }

    // POST /api/survey/responder
    public function responder(Request $request): JsonResponse
    {
        $request->validate([
            'respuestas'         => 'required|array',
            'comentario_general' => 'nullable|string|max:2000',
            'puntuacion_general' => 'nullable|integer|min:1|max:10',
        ]);

        $survey = Survey::where('activa', true)->latest()->firstOrFail();

        if (SurveyResponse::where('survey_id', $survey->id)->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Ya has completado esta encuesta.'], 422);
        }

        $response = SurveyResponse::create([
            'survey_id'          => $survey->id,
            'user_id'            => $request->user()->id,
            'respuestas'         => $request->respuestas,
            'comentario_general' => $request->comentario_general,
            'puntuacion_general' => $request->puntuacion_general,
            'completada_en'      => now(),
        ]);

        return response()->json(['message' => '¡Gracias por completar la encuesta!', 'response' => $response], 201);
    }

    // GET /api/survey/mi-respuesta
    public function miRespuesta(Request $request): JsonResponse
    {
        $survey = Survey::where('activa', true)->latest()->first();
        if (! $survey) return response()->json(null);

        return response()->json(
            SurveyResponse::where('survey_id', $survey->id)
                ->where('user_id', $request->user()->id)
                ->first()
        );
    }

    // GET /api/survey/resultados  — solo admin
    public function resultados(): JsonResponse
    {
        $survey = Survey::where('activa', true)
            ->with(['questions', 'responses.user:id,name,apellido'])
            ->latest()
            ->first();

        if (! $survey) return response()->json(['total_respuestas' => 0, 'estadisticas' => []]);

        $responses = $survey->responses;

        $stats = $survey->questions->map(function ($q) use ($responses) {
            $answers = $responses->map(fn($r) => $r->respuestas[$q->id] ?? null)->filter()->values();

            return [
                'pregunta'   => $q->pregunta,
                'tipo'       => $q->tipo,
                'promedio'   => $q->tipo === 'escala' && $answers->count() ? round($answers->avg(), 1) : null,
                'respuestas' => $q->tipo !== 'escala' ? $answers : null,
                'total'      => $answers->count(),
            ];
        });

        return response()->json([
            'survey'           => $survey->only(['id', 'titulo']),
            'total_respuestas' => $responses->count(),
            'estadisticas'     => $stats,
        ]);
    }

    // GET /api/survey/estadisticas — alias para resultados
    public function estadisticas(): JsonResponse
    {
        return $this->resultados();
    }

    // POST /api/survey/preguntas — solo admin
    public function crearPregunta(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pregunta'  => 'required|string',
            'tipo'      => 'required|in:texto,escala,opciones,multiple',
            'opciones'  => 'nullable|array',
            'orden'     => 'nullable|integer',
            'requerida' => 'boolean',
        ]);

        $survey   = Survey::where('activa', true)->latest()->firstOrFail();
        $question = $survey->questions()->create(array_merge($data, [
            'orden' => $data['orden'] ?? $survey->questions()->count(),
        ]));

        return response()->json($question, 201);
    }
}