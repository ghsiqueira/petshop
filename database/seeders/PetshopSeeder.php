<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Petshop;
use App\Models\User;

class PetshopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $petshopUser = User::whereHas('roles', function($query) {
            $query->where('name', 'petshop');
        })->first();

        Petshop::create([
            'user_id' => $petshopUser->id,
            'name' => 'PetFriends',
            'description' => 'Pet shop completo com serviços de banho, tosa e veterinário.',
            'address' => 'Avenida dos Animais, 789',
            'phone' => '(11) 3333-4444',
            'email' => 'petshop@exemplo.com',
            'logo' => null, // Não temos logo por enquanto
            'is_active' => true,
        ]);
    }
}