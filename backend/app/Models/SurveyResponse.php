<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $fillable = [
        'survey_id', 'user_id', 'respuestas',
        'comentario_general', 'puntuacion_general', 'completada_en',
    ];

    protected function casts(): array
    {
        return [
            'respuestas'    => 'array',
            'completada_en' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}