<?php

namespace Database\Seeders;

use App\Models\CalendarEvent;
use App\Models\Department;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //1. Departamentos
        $desarrollo = Department::create([
            'nombre'      => 'Desarrollo',
            'descripcion' => 'Equipo de ingeniería y desarrollo de software',
            'color'       => '#3B82F6',
        ]);
        $diseno = Department::create([
            'nombre'      => 'Diseño',
            'descripcion' => 'UX/UI y diseño de producto',
            'color'       => '#8B5CF6',
        ]);
        $marketing = Department::create([
            'nombre'      => 'Marketing',
            'descripcion' => 'Comunicación, marca y crecimiento',
            'color'       => '#10B981',
        ]);
        $rrhh = Department::create([
            'nombre'      => 'Recursos Humanos',
            'descripcion' => 'Gestión de personas y cultura',
            'color'       => '#F59E0B',
        ]);
        $ventas = Department::create([
            'nombre'      => 'Ventas',
            'descripcion' => 'Comercial y desarrollo de negocio',
            'color'       => '#EF4444',
        ]);

        //2. Usuarios
        $admin = User::create([
            'name'                => 'Admin',
            'apellido'            => 'Sistema',
            'email'               => 'admin@empresa.com',
            'password'            => Hash::make('password'),
            'rol'                 => 'admin',
            'department_id'       => $rrhh->id,
            'cargo'               => 'Administrador del Sistema',
            'telefono'            => '+34 600 000 001',
            'activo'              => true,
            'fecha_incorporacion' => now()->subYears(2),
        ]);

        $mentor = User::create([
            'name'                => 'María',
            'apellido'            => 'García',
            'email'               => 'mentor@empresa.com',
            'password'            => Hash::make('password'),
            'rol'                 => 'mentor',
            'department_id'       => $desarrollo->id,
            'cargo'               => 'Tech Lead',
            'telefono'            => '+34 600 000 002',
            'activo'              => true,
            'fecha_incorporacion' => now()->subYear(),
        ]);

        $alumno = User::create([
            'name'                => 'Carlos',
            'apellido'            => 'López',
            'email'               => 'alumno@empresa.com',
            'password'            => Hash::make('password'),
            'rol'                 => 'alumno',
            'department_id'       => $desarrollo->id,
            'cargo'               => 'Desarrollador Junior',
            'telefono'            => '+34 600 000 003',
            'activo'              => true,
            'fecha_incorporacion' => now()->subWeeks(2),
        ]);

        //3. Encuesta de Onboarding
        $survey = Survey::create([
            'titulo'      => 'Encuesta de Incorporación',
            'descripcion' => 'Ayúdanos a mejorar el proceso de onboarding compartiendo tu experiencia.',
            'activa'      => true,
            'fecha_inicio'=> now()->subMonth(),
        ]);

        $preguntas = [
            ['pregunta' => '¿Cómo valorarías tu experiencia general de incorporación?',      'tipo' => 'escala',   'orden' => 1, 'requerida' => true],
            ['pregunta' => '¿El proceso de bienvenida fue claro y bien organizado?',          'tipo' => 'escala',   'orden' => 2, 'requerida' => true],
            ['pregunta' => '¿Recibiste toda la información necesaria sobre la empresa?',      'tipo' => 'escala',   'orden' => 3, 'requerida' => true],
            ['pregunta' => '¿Tu equipo te ha acogido bien desde el primer día?',              'tipo' => 'escala',   'orden' => 4, 'requerida' => true],
            ['pregunta' => '¿Las herramientas y accesos estaban listos desde el primer día?', 'tipo' => 'escala',   'orden' => 5, 'requerida' => true],
            ['pregunta' => '¿Cuál es tu aspecto favorito de trabajar aquí?',                  'tipo' => 'texto',    'orden' => 6, 'requerida' => false],
            ['pregunta' => '¿Qué podríamos mejorar en el proceso de onboarding?',             'tipo' => 'texto',    'orden' => 7, 'requerida' => false],
            [
                'pregunta'  => '¿Cómo te enteraste de esta empresa?',
                'tipo'      => 'opciones',
                'orden'     => 8,
                'requerida' => true,
                'opciones'  => ['LinkedIn', 'Referido por alguien', 'Portal de empleo', 'Web de la empresa', 'Otro'],
            ],
        ];

        foreach ($preguntas as $p) {
            SurveyQuestion::create(array_merge(['survey_id' => $survey->id], $p));
        }

        //4. Eventos de Calendario
        $eventos = [
            [
                'titulo'      => 'Bienvenida al equipo',
                'descripcion' => 'Presentación general de la empresa y conocer al equipo.',
                'fecha_inicio'=> now()->startOfWeek()->setHour(10)->setMinute(0),
                'fecha_fin'   => now()->startOfWeek()->setHour(11)->setMinute(0),
                'tipo'        => 'empresa',
                'color'       => '#3B82F6',
                'publico'     => true,
            ],
            [
                'titulo'      => 'Formación en herramientas internas',
                'descripcion' => 'Introducción a Jira, Confluence y GitHub corporativo.',
                'fecha_inicio'=> now()->startOfWeek()->addDays(1)->setHour(9)->setMinute(0),
                'fecha_fin'   => now()->startOfWeek()->addDays(1)->setHour(11)->setMinute(0),
                'tipo'        => 'formacion',
                'color'       => '#8B5CF6',
                'publico'     => true,
            ],
            [
                'titulo'      => 'Reunión semanal de equipo',
                'descripcion' => 'Stand-up semanal de seguimiento del equipo de desarrollo.',
                'fecha_inicio'=> now()->addWeek()->startOfWeek()->setHour(10)->setMinute(0),
                'fecha_fin'   => now()->addWeek()->startOfWeek()->setHour(10)->setMinute(30),
                'tipo'        => 'reunion',
                'color'       => '#10B981',
                'publico'     => true,
            ],
            [
                'titulo'      => 'Revisión periodo de prueba',
                'descripcion' => 'Evaluación del primer mes de incorporación con RRHH.',
                'fecha_inicio'=> now()->addMonths(1)->setHour(12)->setMinute(0),
                'fecha_fin'   => now()->addMonths(1)->setHour(13)->setMinute(0),
                'tipo'        => 'reunion',
                'color'       => '#F59E0B',
                'publico'     => true,
            ],
            [
                'titulo'      => 'Día de la empresa — Fiesta anual',
                'descripcion' => 'Celebración anual de la empresa con todo el equipo.',
                'fecha_inicio'=> now()->addMonths(2)->startOfMonth()->setHour(16)->setMinute(0),
                'fecha_fin'   => now()->addMonths(2)->startOfMonth()->setHour(22)->setMinute(0),
                'tipo'        => 'empresa',
                'color'       => '#EF4444',
                'publico'     => true,
            ],
        ];

        foreach ($eventos as $evento) {
            CalendarEvent::create(array_merge($evento, ['user_id' => $admin->id]));
        }

        //5. Tareas de ejemplo para el alumno
        $tareas = [
            [
                'titulo'          => 'Leer la guía de bienvenida completa',
                'descripcion'     => 'Revisar todos los apartados: cultura, valores, políticas y normas.',
                'estado'          => 'completada',
                'prioridad'       => 'alta',
                'categoria'       => 'formacion',
                'tiempo_estimado' => 2.0,
                'completada_en'   => now()->subDays(10),
                'orden'           => 1,
            ],
            [
                'titulo'          => 'Configurar el entorno de desarrollo',
                'descripcion'     => 'Instalar VS Code, Git, Docker y accesos a repositorios.',
                'estado'          => 'completada',
                'prioridad'       => 'urgente',
                'categoria'       => 'desarrollo',
                'tiempo_estimado' => 4.0,
                'completada_en'   => now()->subDays(8),
                'orden'           => 2,
            ],
            [
                'titulo'          => 'Completar la encuesta de onboarding',
                'descripcion'     => 'Rellenar la encuesta de incorporación para ayudar a RRHH.',
                'estado'          => 'pendiente',
                'prioridad'       => 'media',
                'categoria'       => 'formacion',
                'tiempo_estimado' => 0.5,
                'orden'           => 3,
            ],
            [
                'titulo'          => 'Primera reunión con el mentor',
                'descripcion'     => 'Sesión inicial con María García para establecer objetivos.',
                'estado'          => 'en_progreso',
                'prioridad'       => 'alta',
                'categoria'       => 'reunion',
                'tiempo_estimado' => 1.0,
                'orden'           => 4,
            ],
            [
                'titulo'          => 'Revisar el código del proyecto principal',
                'descripcion'     => 'Explorar el repositorio, entender la arquitectura y la documentación.',
                'estado'          => 'pendiente',
                'prioridad'       => 'media',
                'categoria'       => 'desarrollo',
                'tiempo_estimado' => 3.0,
                'orden'           => 5,
            ],
            [
                'titulo'          => 'Asistir a la formación de herramientas internas',
                'descripcion'     => 'Sesión de formación sobre Jira, Confluence y flujos de trabajo.',
                'estado'          => 'pendiente',
                'prioridad'       => 'alta',
                'categoria'       => 'formacion',
                'tiempo_estimado' => 2.0,
                'orden'           => 6,
            ],
        ];

        foreach ($tareas as $tarea) {
            Task::create(array_merge($tarea, ['user_id' => $alumno->id]));
        }

        //Resumen
        $this->command->info('');
        $this->command->info('✅  Base de datos inicializada correctamente.');
        $this->command->info('');
        $this->command->info('   Departamentos : 5');
        $this->command->info('   Usuarios      : 3');
        $this->command->info('   Preguntas     : ' . count($preguntas));
        $this->command->info('   Eventos       : ' . count($eventos));
        $this->command->info('   Tareas        : ' . count($tareas));
        $this->command->info('');
        $this->command->info('👤  admin@empresa.com   / password  →  Admin');
        $this->command->info('👤  mentor@empresa.com  / password  →  Mentor');
        $this->command->info('👤  alumno@empresa.com  / password  →  Alumno');
        $this->command->info('');
    }
}