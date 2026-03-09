<?php

namespace App\Http\Controllers;

use App\Models\MentorChat;
use App\Services\ClaudeAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MentorController extends Controller
{
    public function __construct(protected ClaudeAIService $ai) {}

    // POST /api/mentor/chat
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'mensaje' => 'required|string|max:2000',
        ]);

        $user = $request->user();

        // Últimas 10 interacciones como contexto
        $historial = MentorChat::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($c) => [
                'usuario' => $c->mensaje_usuario,
                'mentor'  => $c->respuesta_ia,
            ])
            ->toArray();

        $respuesta = $this->ai->mentorChat($historial, $request->mensaje);

        $chat = MentorChat::create([
            'user_id'         => $user->id,
            'mensaje_usuario' => $request->mensaje,
            'respuesta_ia'    => $respuesta['content'],
            'tokens_usados'   => $respuesta['tokens']['output_tokens'] ?? 0,
        ]);

        return response()->json([
            'respuesta' => $respuesta['content'],
            'chat_id'   => $chat->id,
        ]);
    }

    // GET /api/mentor/historial
    public function historial(Request $request): JsonResponse
    {
        $chats = MentorChat::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($c) => [
                'id'        => $c->id,
                'usuario'   => $c->mensaje_usuario,
                'mentor'    => $c->respuesta_ia,
                'timestamp' => $c->created_at->toIso8601String(),
            ]);

        return response()->json($chats);
    }

    // DELETE /api/mentor/historial
    public function limpiarHistorial(Request $request): JsonResponse
    {
        MentorChat::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Historial de conversación limpiado.']);
    }
}