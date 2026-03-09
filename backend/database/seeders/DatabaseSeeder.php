<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\CalendarEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    //use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //Departamentos
        $depts = [];
        $deptsData = [
            ['nombre' => 'Desarrollo',       'color' => '#3B82F6'],
            ['nombre' => 'Diseño',           'color' => '#8B5CF6'],
            ['nombre' => 'Marketing',        'color' => '#10B981'],
            ['nombre' => 'Recursos Humanos', 'color' => '#F59E0B'],
            ['nombre' => 'Ventas',           'color' => '#EF4444'],
        ];
        foreach ($deptsData as $d) {
            $depts[] = DB::table('departments')->insertGetId(
                array_merge($d, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        //Usuarios
        $admin = User::create([
            'name'                => 'Admin',
            'apellido'            => 'Sistema',
            'email'               => 'admin@empresa.com',
            'password'            => Hash::make('password'),
            'rol'                 => 'admin',
            'department_id'       => $depts[3],
            'cargo'               => 'Administrador del Sistema',
            'activo'              => true,
            'fecha_incorporacion' => now()->subYears(2),
        ]);

        User::create([
            'name'                => 'María',
            'apellido'            => 'García',
            'email'               => 'mentor@empresa.com',
            'password'            => Hash::make('password'),
            'rol'                 => 'mentor',
            'department_id'       => $depts[0],
            'cargo'               => 'Tech Lead',
            'activo'              => true,
            'fecha_incorporacion' => now()->subYear(),
        ]);

        User::create([
            'name'                => 'Carlos',
            'apellido'            => 'López',
            'email'               => 'alumno@empresa.com',
            'password'            => Hash::make('password'),
            'rol'                 => 'alumno',
            'department_id'       => $depts[0],
            'cargo'               => 'Desarrollador Junior',
            'activo'              => true,
            'fecha_incorporacion' => now()->subWeeks(2),
        ]);

        //Encuesta de Onboarding
        $survey = Survey::create([
            'titulo'      => 'Encuesta de Incorporación',
            'descripcion' => 'Ayúdanos a mejorar el proceso de onboarding compartiendo tu experiencia.',
            'activa'      => true,
        ]);

        $preguntas = [
            ['pregunta' => '¿Cómo valorarías tu experiencia general de incorporación?',    'tipo' => 'escala',   'orden' => 1],
            ['pregunta' => '¿El proceso de bienvenida fue claro y bien organizado?',        'tipo' => 'escala',   'orden' => 2],
            ['pregunta' => '¿Recibiste toda la información necesaria sobre la empresa?',    'tipo' => 'escala',   'orden' => 3],
            ['pregunta' => '¿Tu equipo te ha acogido bien desde el primer día?',            'tipo' => 'escala',   'orden' => 4],
            ['pregunta' => '¿Las herramientas y accesos estaban listos desde el primer día?', 'tipo' => 'escala', 'orden' => 5],
            ['pregunta' => '¿Cuál es tu aspecto favorito de trabajar aquí?',                'tipo' => 'texto',    'orden' => 6],
            ['pregunta' => '¿Qué podríamos mejorar en el proceso de onboarding?',           'tipo' => 'texto',    'orden' => 7],
            [
                'pregunta' => '¿Cómo te enteraste de esta empresa?',
                'tipo'     => 'opciones',
                'orden'    => 8,
                'opciones' => json_encode(['LinkedIn', 'Referido por alguien', 'Portal de empleo', 'Web de la empresa', 'Otro']),
            ],
        ];

        foreach ($preguntas as $p) {
            SurveyQuestion::create(array_merge(['survey_id' => $survey->id, 'requerida' => true], $p));
        }

        //Eventos de Calendario
        $eventos = [
            [
                'titulo'      => 'Bienvenida al equipo',
                'descripcion' => 'Presentación general de la empresa y conocer al equipo',
                'fecha_inicio'=> now()->startOfWeek()->setHour(10),
                'fecha_fin'   => now()->startOfWeek()->setHour(11),
                'tipo'        => 'empresa',
                'color'       => '#3B82F6',
                'publico'     => true,
            ],
            [
                'titulo'      => 'Formación en herramientas internas',
                'descripcion' => 'Introducción a Jira, Confluence y GitHub corporativo',
                'fecha_inicio'=> now()->startOfWeek()->addDays(1)->setHour(9),
                'fecha_fin'   => now()->startOfWeek()->addDays(1)->setHour(11),
                'tipo'        => 'formacion',
                'color'       => '#8B5CF6',
                'publico'     => true,
            ],
            [
                'titulo'      => 'Reunión semanal de equipo',
                'descripcion' => 'Stand-up semanal de seguimiento',
                'fecha_inicio'=> now()->addWeek()->startOfWeek()->setHour(10),
                'fecha_fin'   => now()->addWeek()->startOfWeek()->setHour(11),
                'tipo'        => 'reunion',
                'color'       => '#10B981',
                'publico'     => true,
            ],
            [
                'titulo'      => 'Revisión periodo de prueba',
                'descripcion' => 'Evaluación del primer mes de incorporación',
                'fecha_inicio'=> now()->addMonths(1)->setHour(12),
                'fecha_fin'   => now()->addMonths(1)->setHour(13),
                'tipo'        => 'reunion',
                'color'       => '#F59E0B',
                'publico'     => true,
            ],
        ];

        foreach ($eventos as $evento) {
            CalendarEvent::create(array_merge($evento, ['user_id' => $admin->id]));
        }

        $this->command->info('');
        $this->command->info('✅  Base de datos inicializada correctamente.');
        $this->command->info('');
        $this->command->info('👤  admin@empresa.com  / password  (Admin)');
        $this->command->info('👤  mentor@empresa.com / password  (Mentor)');
        $this->command->info('👤  alumno@empresa.com / password  (Alumno)');
        $this->command->info('');

    }
}
