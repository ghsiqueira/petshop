<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pet;
use App\Models\User;
use Carbon\Carbon;

class PetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = User::whereHas('roles', function($query) {
            $query->where('name', 'client');
        })->first();

        $client2 = User::whereHas('roles', function($query) {
            $query->where('name', 'client');
        })->skip(1)->first();

        // Cão adulto (3 anos e 6 meses atrás)
        Pet::create([
            'user_id' => $client->id,
            'name' => 'Rex',
            'species' => 'dog',
            'breed' => 'Labrador',
            'birth_date' => Carbon::now()->subYears(3)->subMonths(6),
            'gender' => 'male',
            'medical_information' => 'Vacinado e vermifugado. Alérgico a alguns tipos de shampoo.',
            'photo' => null, // Sem foto por enquanto
        ]);

        // Gato jovem (1 ano e 3 meses atrás)
        Pet::create([
            'user_id' => $client->id,
            'name' => 'Luna',
            'species' => 'cat',
            'breed' => 'Siamês',
            'birth_date' => Carbon::now()->subYears(1)->subMonths(3),
            'gender' => 'female',
            'medical_information' => 'Castrada. Vacinas em dia.',
            'photo' => null,
        ]);

        // Filhote (4 meses atrás)
        Pet::create([
            'user_id' => $client2->id,
            'name' => 'Toby',
            'species' => 'dog',
            'breed' => 'Shih Tzu',
            'birth_date' => Carbon::now()->subMonths(4),
            'gender' => 'male',
            'medical_information' => 'Primeira dose de vacina aplicada.',
            'photo' => null,
        ]);

        // Pet mais velho (8 anos atrás)
        Pet::create([
            'user_id' => $client2->id,
            'name' => 'Mel',
            'species' => 'dog',
            'breed' => 'Poodle',
            'birth_date' => Carbon::now()->subYears(8),
            'gender' => 'female',
            'medical_information' => 'Idosa. Dieta especial. Problemas articulares.',
            'photo' => null,
        ]);
    }
}