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
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [

            "model" => "gpt-4o-mini",

            "messages" => [
                [
                    "role" => "system",
                    "content" => "Eres el mentor virtual del programa Dualab que ayuda a alumnos en prácticas con dudas sobre horarios, tutorías y funcionamiento del programa."
                ],
                [
                    "role" => "user",
                    "content" => $request->message
                ]
            ],

            "temperature" => 0.7
        ]);

        return $response->json();
    }
}