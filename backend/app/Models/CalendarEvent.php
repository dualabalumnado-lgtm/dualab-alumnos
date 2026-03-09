<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $fillable = [
        'user_id', 'titulo', 'descripcion',
        'fecha_inicio', 'fecha_fin', 'todo_el_dia',
        'color', 'tipo', 'publico',
        'ubicacion', 'url_reunion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin'    => 'datetime',
            'todo_el_dia'  => 'boolean',
            'publico'      => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}