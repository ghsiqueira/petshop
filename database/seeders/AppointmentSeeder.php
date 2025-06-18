<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Employee;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pets = Pet::all();
        $services = Service::all();
        $employee = Employee::first();

        // Agendamento passado (concluído) - user 1
        Appointment::create([
            'user_id' => 1,
            'pet_id' => $pets[0]->id,
            'service_id' => $services[0]->id,
            'employee_id' => $employee->id,
            'appointment_datetime' => Carbon::now()->subDays(10)->setHour(14)->setMinute(0),
            'status' => 'completed',
            'notes' => 'Cliente satisfeito com o serviço.',
        ]);

        // Agendamento passado (cancelado) - user 5
        Appointment::create([
            'user_id' => 5,
            'pet_id' => $pets[1]->id,
            'service_id' => $services[1]->id,
            'employee_id' => $employee->id,
            'appointment_datetime' => Carbon::now()->subDays(5)->setHour(10)->setMinute(30),
            'status' => 'cancelled',
            'notes' => 'Cliente cancelou por motivos pessoais.',
        ]);

        // Agendamento futuro (confirmado) - user 1
        Appointment::create([
            'user_id' => 1,
            'pet_id' => $pets[0]->id,
            'service_id' => $services[0]->id,
            'employee_id' => $employee->id,
            'appointment_datetime' => Carbon::now()->addDays(3)->setHour(15)->setMinute(0),
            'status' => 'confirmed',
            'notes' => 'Cliente pediu atenção especial às orelhas.',
        ]);

        // Agendamento futuro (pendente) - user 5
        Appointment::create([
            'user_id' => 5,
            'pet_id' => $pets[2]->id,
            'service_id' => $services[1]->id,
            'employee_id' => $employee->id,
            'appointment_datetime' => Carbon::now()->addDays(5)->setHour(9)->setMinute(0),
            'status' => 'pending',
            'notes' => 'Primeira visita do pet ao petshop.',
        ]);

        // Agendamento para consulta veterinária - user 1
        Appointment::create([
            'user_id' => 1,
            'pet_id' => $pets[3]->id,
            'service_id' => $services[2]->id,
            'employee_id' => $employee->id,
            'appointment_datetime' => Carbon::now()->addDays(2)->setHour(11)->setMinute(30),
            'status' => 'confirmed',
            'notes' => 'Revisão de rotina para pet idoso.',
        ]);
    }
}