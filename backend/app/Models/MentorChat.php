<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorChat extends Model
{
    protected $fillable = [
        'user_id', 'mensaje_usuario', 'respuesta_ia', 'tokens_usados',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}