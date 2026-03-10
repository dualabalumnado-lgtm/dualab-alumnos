<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apellido',
        'email',
        'password',
        'rol',
        'department_id',
        'cargo',
        'telefono',
        'avatar',
        'activo',
        'ultimo_acceso',
        'fecha_incorporacion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'ultimo_acceso'       => 'datetime',
            'fecha_incorporacion' => 'datetime',
            'activo'              => 'boolean',
            'password'            => 'hashed',
        ];
    }

    //Relaciones

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function fichajes()
    {
        return $this->hasMany(Fichaje::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function mentorChats()
    {
        return $this->hasMany(MentorChat::class);
    }

    public function surveyResponses()
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    //Helpers

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->name . ' ' . $this->apellido);
    }

    public function getInicialAttribute(): string
    {
        $nombre   = $this->name[0]     ?? '';
        $apellido = $this->apellido[0] ?? '';
        return strtoupper($nombre . $apellido);
    }

    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function isMentor(): bool
    {
        return $this->rol === 'mentor';
    }

    public function isAlumno(): bool
    {
        return $this->rol === 'alumno';
    }
}