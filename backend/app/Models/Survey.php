<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['titulo', 'descripcion', 'activa', 'fecha_inicio', 'fecha_fin'];

    protected function casts(): array
    {
        return [
            'activa'       => 'boolean',
            'fecha_inicio' => 'datetime',
            'fecha_fin'    => 'datetime',
        ];
    }

    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('orden');
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
}