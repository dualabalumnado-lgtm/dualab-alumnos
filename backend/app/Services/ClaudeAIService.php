<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://api.anthropic.com/v1';

    protected string $companyContext = <<<CONTEXT
Eres el Mentor Virtual de OnboardingPro, asistente de incorporación de nuevos empleados.

SOBRE LA EMPRESA:
- Empresa de tecnología enfocada en soluciones digitales innovadoras
- Departamentos: Desarrollo, Diseño, Marketing, Recursos Humanos, Ventas y Soporte
- Horario laboral: Lunes a Viernes de 9:00 a 18:00 (1h de almuerzo de 14:00 a 15:00)
- Sede central en Madrid, oficina secundaria en Barcelona
- Teletrabajo híbrido disponible tras el período de prueba (3 meses)

PROCESO DE ONBOARDING:
1. Semana 1: Bienvenida, acceso a herramientas, conocer al equipo
2. Semana 2-3: Formación específica del área
3. Semana 4: Proyectos menores con supervisión del mentor
4. Mes 2: Integración completa en el equipo
5. Mes 3: Evaluación del período de prueba

HERRAMIENTAS DE LA EMPRESA:
- Comunicación: Slack (mensajería) + correo corporativo (Google Workspace)
- Gestión de proyectos: Jira + Confluence (documentación)
- Repositorios: GitHub / GitLab
- Videollamadas: Google Meet
- Diseño: Figma
- Esta plataforma: OnboardingPro (fichaje, calendario, tareas, encuesta)

NORMAS IMPORTANTES:
- El fichaje de entrada debe hacerse antes de las 9:15h
- Las vacaciones se solicitan con al menos 2 semanas de antelación en RRHH
- Reuniones de equipo: los lunes a las 10:00h
- El código de conducta completo está en Confluence > RRHH > Normativa

RESPONDE SIEMPRE:
- En español
- Con un tono amigable, cercano y profesional
- Con información precisa y útil para el empleado
- Si desconoces algo específico, indica que consulte con RRHH o su responsable directo
CONTEXT;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
        $this->model  = config('services.anthropic.model', 'claude-sonnet-4-20250514');
    }

    //Mentor Virtual

    public function mentorChat(array $historial, string $mensajeUsuario): array
    {
        $messages = [];

        foreach ($historial as $entry) {
            $messages[] = ['role' => 'user',      'content' => $entry['usuario']];
            $messages[] = ['role' => 'assistant', 'content' => $entry['mentor']];
        }

        $messages[] = ['role' => 'user', 'content' => $mensajeUsuario];

        $response = $this->request('/messages', [
            'model'      => $this->model,
            'max_tokens' => 1024,
            'system'     => $this->companyContext,
            'messages'   => $messages,
        ]);

        return [
            'content' => $response['content'][0]['text'] ?? 'No se pudo obtener respuesta.',
            'tokens'  => $response['usage'] ?? [],
        ];
    }

    //Generador de Tareas

    public function generarTarea(string $descripcion): array
    {
        $hoy    = now()->format('Y-m-d');
        $prompt = <<<PROMPT
El usuario quiere crear una tarea con esta descripción: "{$descripcion}"
Fecha actual: {$hoy}

Genera una tarea estructurada en formato JSON con exactamente estos campos:
{
  "titulo": "string corto y accionable (máx 80 caracteres)",
  "descripcion": "descripción detallada en 2-3 frases",
  "prioridad": "baja|media|alta|urgente",
  "categoria": "desarrollo|diseño|reunion|documentacion|revision|formacion|otro",
  "tiempo_estimado": número (horas, entre 0.5 y 40),
  "fecha_sugerida": "YYYY-MM-DD (fecha razonable según la urgencia)",
  "subtareas": ["paso 1", "paso 2", "paso 3"],
  "etiquetas": ["etiqueta1", "etiqueta2"]
}

Responde ÚNICAMENTE con el JSON válido, sin texto adicional, sin bloques de código.
PROMPT;

        $response = $this->request('/messages', [
            'model'      => $this->model,
            'max_tokens' => 1024,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);

        $text = $response['content'][0]['text'] ?? '{}';
        $text = preg_replace('/```(?:json)?\s*|\s*```/', '', trim($text));

        return json_decode($text, true) ?? ['error' => 'No se pudo parsear la respuesta de la IA.'];
    }

    public function generarMultiplesTareas(string $objetivo, int $cantidad = 5): array
    {
        $prompt = <<<PROMPT
El usuario quiere descomponer este objetivo en tareas: "{$objetivo}"

Genera exactamente {$cantidad} tareas en formato JSON array. Cada tarea debe tener:
{
  "titulo": "string",
  "descripcion": "string",
  "prioridad": "baja|media|alta|urgente",
  "categoria": "string",
  "tiempo_estimado": número (horas),
  "orden": número (1, 2, 3... en orden lógico de ejecución)
}

Las tareas deben estar ordenadas lógicamente para completar el objetivo.
Responde ÚNICAMENTE con el array JSON válido, sin texto adicional.
PROMPT;

        $response = $this->request('/messages', [
            'model'      => $this->model,
            'max_tokens' => 2048,
            'messages'   => [['role' => 'user', 'content' => $prompt]],
        ]);

        $text = $response['content'][0]['text'] ?? '[]';
        $text = preg_replace('/```(?:json)?\s*|\s*```/', '', trim($text));

        return json_decode($text, true) ?? [];
    }

    //HTTP Helper

    protected function request(string $endpoint, array $payload): array
    {

        if (empty($this->apiKey)) {
            throw new \RuntimeException(
                'El Mentor Virtual no está disponible en este momento. Contacta con el administrador.'
            );
        }

        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(30)->post($this->baseUrl . $endpoint, $payload);

        if ($response->failed()) {
            Log::error('Anthropic API error', [
                'status'   => $response->status(),
                'endpoint' => $endpoint,
                'body'     => $response->body(),
            ]);
            throw new \RuntimeException('Error en la API de IA: HTTP ' . $response->status());
        }

        return $response->json();
    }
}