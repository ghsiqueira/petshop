<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Petshop;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $petshop = Petshop::first();

        // Banho e Tosa
        Service::create([
            'petshop_id' => $petshop->id,
            'name' => 'Banho e Tosa Completa',
            'description' => 'Serviço completo de banho e tosa, incluindo corte de unhas, limpeza de ouvidos e escovas.',
            'price' => 80.00,
            'duration_minutes' => 60, // em minutos
            'is_active' => true,
        ]);

        // Banho
        Service::create([
            'petshop_id' => $petshop->id,
            'name' => 'Banho',
            'description' => 'Banho com shampoo hipoalergênico, secagem e escovação.',
            'price' => 50.00,
            'duration_minutes' => 30, // em minutos
            'is_active' => true,
        ]);

        // Consulta Veterinária
        Service::create([
            'petshop_id' => $petshop->id,
            'name' => 'Consulta Veterinária',
            'description' => 'Avaliação completa de saúde com veterinário especializado.',
            'price' => 120.00,
            'duration_minutes' => 45, // em minutos
            'is_active' => true,
        ]);
    }
}