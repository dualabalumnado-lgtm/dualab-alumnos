<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fichaje extends Model
{
    protected $fillable = [
        'user_id', 'fecha',
        'hora_entrada', 'hora_salida',
        'ip_entrada', 'ip_salida',
        'lat_entrada', 'lng_entrada',
        'lat_salida',  'lng_salida',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha'        => 'date',
            'hora_entrada' => 'datetime',
            'hora_salida'  => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}