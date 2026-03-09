<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    protected $fillable = ['survey_id', 'pregunta', 'tipo', 'opciones', 'orden', 'requerida'];

    protected function casts(): array
    {
        return [
            'opciones'  => 'array',
            'requerida' => 'boolean',
        ];
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}