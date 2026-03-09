<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'user_id', 'titulo', 'descripcion',
        'estado', 'prioridad', 'categoria',
        'tiempo_estimado', 'fecha_vencimiento',
        'subtareas', 'etiquetas',
        'generada_con_ia', 'orden', 'completada_en',
    ];

    protected function casts(): array
    {
        return [
            'subtareas'         => 'array',
            'etiquetas'         => 'array',
            'generada_con_ia'   => 'boolean',
            'fecha_vencimiento' => 'date',
            'completada_en'     => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}