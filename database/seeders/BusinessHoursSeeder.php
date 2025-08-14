<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Petshop;
use App\Models\Service;

class BusinessHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🕐 Configurando horários de funcionamento...');

        // Horários padrão para todos os petshops
        $defaultBusinessHours = [
            'monday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'tuesday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'wednesday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'thursday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'friday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'saturday' => ['open' => '08:00', 'close' => '16:00', 'enabled' => true],
            'sunday' => ['open' => '09:00', 'close' => '15:00', 'enabled' => false],
        ];

        // Atualizar todos os petshops existentes
        $updated = 0;
        Petshop::chunk(50, function ($petshops) use ($defaultBusinessHours, &$updated) {
            foreach ($petshops as $petshop) {
                // Configurar horários do petshop se ainda não estiver configurado
                if (empty($petshop->business_hours)) {
                    $petshop->update([
                        'business_hours' => $defaultBusinessHours,
                        'slot_duration' => 30,
                        'advance_booking_days' => 30,
                        'allow_weekend_booking' => true,
                        'lunch_break_start' => '12:00',
                        'lunch_break_end' => '13:00',
                    ]);
                    $updated++;
                }

                // Configurar serviços para usar horários do petshop
                $petshop->services()->update([
                    'use_petshop_hours' => true,
                    'buffer_time' => 0
                ]);
            }
        });

        $this->command->info("✅ {$updated} petshops configurados com horários padrão!");
        
        // Configurar alguns serviços com horários específicos (exemplo)
        $this->setupSpecialServiceHours();
        
        $this->command->info('🎉 Configuração de horários concluída!');
    }

    /**
     * Configurar alguns serviços com horários especiais
     */
    private function setupSpecialServiceHours()
    {
        // Exemplo: Serviços de banho só de manhã
        $bathServices = Service::where('name', 'LIKE', '%banho%')
            ->orWhere('name', 'LIKE', '%tosa%')
            ->limit(5)
            ->get();

        foreach ($bathServices as $service) {
            $service->update([
                'use_petshop_hours' => false,
                'available_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '12:00', 'enabled' => true],
                    'tuesday' => ['open' => '08:00', 'close' => '12:00', 'enabled' => true],
                    'wednesday' => ['open' => '08:00', 'close' => '12:00', 'enabled' => true],
                    'thursday' => ['open' => '08:00', 'close' => '12:00', 'enabled' => true],
                    'friday' => ['open' => '08:00', 'close' => '12:00', 'enabled' => true],
                    'saturday' => ['open' => '08:00', 'close' => '11:00', 'enabled' => true],
                    'sunday' => ['open' => '09:00', 'close' => '11:00', 'enabled' => false],
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'],
                'buffer_time' => 15
            ]);
        }

        // Exemplo: Consultas veterinárias à tarde
        $vetServices = Service::where('name', 'LIKE', '%consulta%')
            ->orWhere('name', 'LIKE', '%veterinár%')
            ->limit(3)
            ->get();

        foreach ($vetServices as $service) {
            $service->update([
                'use_petshop_hours' => false,
                'available_hours' => [
                    'monday' => ['open' => '14:00', 'close' => '18:00', 'enabled' => true],
                    'tuesday' => ['open' => '14:00', 'close' => '18:00', 'enabled' => true],
                    'wednesday' => ['open' => '14:00', 'close' => '18:00', 'enabled' => true],
                    'thursday' => ['open' => '14:00', 'close' => '18:00', 'enabled' => true],
                    'friday' => ['open' => '14:00', 'close' => '18:00', 'enabled' => true],
                    'saturday' => ['open' => '14:00', 'close' => '16:00', 'enabled' => false],
                    'sunday' => ['open' => '09:00', 'close' => '15:00', 'enabled' => false],
                ],
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'buffer_time' => 30
            ]);
        }

        $this->command->info('🎯 Serviços especiais configurados (banho manhã, consultas tarde)');
    }
}
