<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MentorController extends Controller
{
    public function chat(Request $request)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [

            "model" => "gpt-4o-mini",

            "messages" => [
                [
                    "role" => "system",
                    "content" => "Eres el mentor virtual del programa de formación en prácticas. 
                    Ayudas a los alumnos a entender el funcionamiento del programa, las normas,
                    el proceso de aprendizaje y resolver dudas generales."
                ],
                [
                    "role" => "user",
                    "content" => $request->message
                ]
            ]
        ]);

        return $response->json();
    }
}