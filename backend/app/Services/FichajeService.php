<?php

namespace App\Services;

use App\Models\Fichaje;
use Carbon\Carbon;

class FichajeService
{
    public function getEstado(?Fichaje $fichaje): string
    {
        if (! $fichaje)               return 'sin_fichar';
        if ($fichaje->hora_salida)    return 'jornada_completa';
        return 'en_jornada';
    }

    public function calcularHoras(Fichaje $fichaje): array
    {
        if (! $fichaje->hora_entrada) {
            return ['total_minutos' => 0, 'horas' => 0, 'minutos' => 0, 'formato' => '0:00'];
        }

        $inicio       = Carbon::parse($fichaje->hora_entrada);
        $fin          = $fichaje->hora_salida ? Carbon::parse($fichaje->hora_salida) : now();
        $totalMinutos = max(0, $inicio->diffInMinutes($fin));
        $horas        = intdiv($totalMinutos, 60);
        $minutos      = $totalMinutos % 60;

        return [
            'total_minutos' => $totalMinutos,
            'horas'         => $horas,
            'minutos'       => $minutos,
            'formato'       => sprintf('%d:%02d', $horas, $minutos),
        ];
    }

    public function totalHoras(iterable $fichajes): array
    {
        $totalMinutos = 0;

        foreach ($fichajes as $f) {
            if (is_array($f)) {
                $totalMinutos += $f['horas_trabajadas']['total_minutos'] ?? 0;
            } else {
                $totalMinutos += $this->calcularHoras($f)['total_minutos'];
            }
        }

        return [
            'total_minutos' => $totalMinutos,
            'horas'         => intdiv($totalMinutos, 60),
            'minutos'       => $totalMinutos % 60,
            'formato'       => sprintf('%d:%02d', intdiv($totalMinutos, 60), $totalMinutos % 60),
        ];
    }
}